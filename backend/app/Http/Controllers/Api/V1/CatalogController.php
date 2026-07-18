<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    /**
     * GET /api/v1/products
     * Advanced dynamic filtering, sorting, pagination, and search suggestion.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $categorySlug = $request->input('category');
        $brandsSlugs = $request->input('brand'); // array or comma list
        $sizesNames = $request->input('size');   // array or comma list
        $colorsNames = $request->input('color'); // array or comma list
        $priceRange = $request->input('price');   // e.g. "0-5000"
        $sort = $request->input('sort', 'recommended');
        $perPage = (int) $request->input('per_page', 12);

        $query = Product::where('is_active', true)
            ->with(['brand', 'label', 'media', 'variants.size', 'variants.color']);

        // 1. Category Filter (resolves nested categories recursively)
        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                // Fetch all child category IDs to include products in subcategories
                $categoryIds = Category::where('parent_id', $category->id)
                    ->pluck('id')
                    ->push($category->id)
                    ->toArray();

                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            }
        }

        // 2. Search Autocomplete Matcher
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($qb) use ($search) {
                        $qb->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 3. Brand Filter
        if ($brandsSlugs) {
            $brands = is_array($brandsSlugs) ? $brandsSlugs : explode(',', $brandsSlugs);
            $query->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        // 4. Variant Attributes - Size
        if ($sizesNames) {
            $sizes = is_array($sizesNames) ? $sizesNames : explode(',', $sizesNames);
            $query->whereHas('variants.size', function ($q) use ($sizes) {
                $q->whereIn('name', $sizes);
            });
        }

        // 5. Variant Attributes - Color
        if ($colorsNames) {
            $colors = is_array($colorsNames) ? $colorsNames : explode(',', $colorsNames);
            $query->whereHas('variants.color', function ($q) use ($colors) {
                $q->whereIn('name', $colors);
            });
        }

        // 6. Price Range Filter
        if ($priceRange) {
            $parts = explode('-', $priceRange);
            if (count($parts) === 2) {
                $min = (float) $parts[0];
                $max = (float) $parts[1];
                $query->whereHas('variants', function ($q) use ($min, $max) {
                    $q->whereBetween('price', [$min, $max])
                        ->orWhereBetween('sale_price', [$min, $max]);
                });
            }
        }

        // 7. Sorting
        switch ($sort) {
            case 'price_asc':
                $query->whereHas('variants', function ($q) {
                    $q->orderBy('price', 'asc');
                });
                break;
            case 'price_desc':
                $query->whereHas('variants', function ($q) {
                    $q->orderBy('price', 'desc');
                });
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'recommended':
            default:
                $query->orderBy('sort_order', 'asc');
                break;
        }

        $products = $query->paginate($perPage);

        // Compile Dynamic Filters Options lists for sidebar based on category stock matches
        $filters = Cache::remember("filters_{$categorySlug}", 3600, function () {
            $brands = Brand::where('is_active', true)->select('name', 'slug')->get();
            $sizes = Size::select('name', 'slug', 'type')->get();
            $colors = Color::select('name', 'hex_code')->get();

            return [
                [
                    'key' => 'brand',
                    'label' => 'Brand',
                    'style' => 'checkbox',
                    'options' => $brands->map(fn ($b) => ['label' => $b->name, 'value' => $b->slug])->values()->toArray(),
                ],
                [
                    'key' => 'size',
                    'label' => 'Size',
                    'style' => 'checkbox',
                    'options' => $sizes->map(fn ($s) => ['label' => $s->name, 'value' => $s->name])->values()->toArray(),
                ],
                [
                    'key' => 'color',
                    'label' => 'Color',
                    'style' => 'swatch',
                    'options' => $colors->map(fn ($c) => ['label' => $c->name, 'value' => $c->name, 'hex' => $c->hex_code])->values()->toArray(),
                ],
                [
                    'key' => 'price',
                    'label' => 'Price Range',
                    'style' => 'radio',
                    'options' => [
                        ['label' => 'Rs. 299 to Rs. 4999', 'value' => '299-4999'],
                        ['label' => 'Rs. 4999 to Rs. 9699', 'value' => '4999-9699'],
                        ['label' => 'Rs. 9699 to Rs. 14399', 'value' => '9699-14399'],
                        ['label' => 'Rs. 14399 to Rs. 19099', 'value' => '14399-19099'],
                    ],
                ],
            ];
        });

        return response()->json([
            'products' => $products->items(),
            'filters' => $filters,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/products/{id_or_slug}
     */
    public function show(string $idOrSlug): JsonResponse
    {
        $product = Cache::rememberForever("product_{$idOrSlug}", function () use ($idOrSlug) {
            $prod = Product::where('id', $idOrSlug)
                ->orWhere('slug', $idOrSlug)
                ->with(['brand', 'label', 'media', 'variants.size', 'variants.color', 'reviews.user'])
                ->first();
            return $prod ? $prod->toArray() : null;
        });

        if (! $product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $productId = $product['id'];

        // Get similar products based on the main category
        $similar = Cache::remember("similar_products_{$productId}", 3600, function () use ($productId) {
            $catId = \Illuminate\Support\Facades\DB::table('product_category')->where('product_id', $productId)->value('category_id');
            if (! $catId) {
                return [];
            }

            return Product::where('id', '!=', $productId)
                ->whereHas('categories', function ($q) use ($catId) {
                    $q->where('categories.id', $catId);
                })
                ->where('is_active', true)
                ->with(['brand', 'media', 'variants'])
                ->limit(5)
                ->get()
                ->toArray();
        });

        return response()->json([
            'product' => $product,
            'variants' => $product['variants'] ?? [],
            'reviews' => $product['reviews'] ?? [],
            'similar' => $similar,
        ]);
    }

    /**
     * GET /api/v1/categories
     */
    public function categories(): JsonResponse
    {
        $tree = Cache::rememberForever('categories_tree', function () {
            return Category::whereNull('parent_id')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereHas('products')
                        ->orWhereHas('children', function ($sq) {
                            $sq->where('is_active', true)->whereHas('products');
                        });
                })
                ->with(['children' => function ($q) {
                    $q->where('is_active', true)->whereHas('products');
                }])
                ->orderBy('sort_order')
                ->get()
                ->toArray();
        });

        return response()->json($tree);
    }
}
