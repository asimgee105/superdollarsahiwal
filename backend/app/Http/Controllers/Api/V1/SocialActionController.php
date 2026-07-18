<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\RecentlyViewed;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialActionController extends Controller
{
    /**
     * POST /api/v1/wishlist
     * Toggle item in wishlist (supports auth user_id and guest session keys).
     */
    public function toggleWishlist(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        $sessionKey = $request->input('session_key');
        $userId = auth('sanctum')->id();

        if (! $userId && ! $sessionKey) {
            return response()->json(['error' => 'User ID or Guest Session Key is required.'], 400);
        }

        $query = Wishlist::where('product_id', $productId);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_key', $sessionKey);
        }

        $existing = $query->first();
        if ($existing) {
            $existing->delete();
            $status = 'removed';
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'session_key' => $userId ? null : $sessionKey,
                'product_id' => $productId,
            ]);
            $status = 'added';
        }

        return response()->json(['status' => $status]);
    }

    /**
     * POST /api/v1/compare
     */
    public function compare(Request $request): JsonResponse
    {
        $productIds = $request->input('product_ids', []);
        $products = Product::whereIn('id', $productIds)
            ->with(['brand', 'media', 'variants.size', 'variants.color'])
            ->get();

        return response()->json($products);
    }

    /**
     * POST /api/v1/recently-viewed
     */
    public function trackRecentlyViewed(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        $sessionKey = $request->input('session_key');
        $userId = auth('sanctum')->id();

        RecentlyViewed::updateOrCreate(
            [
                'user_id' => $userId,
                'session_key' => $userId ? null : $sessionKey,
                'product_id' => $productId,
            ],
            ['viewed_at' => now()]
        );

        return response()->json(['status' => 'tracked']);
    }

    /**
     * GET /api/v1/recently-viewed
     */
    public function getRecentlyViewed(Request $request): JsonResponse
    {
        $sessionKey = $request->input('session_key');
        $userId = auth('sanctum')->id();

        $query = RecentlyViewed::with(['product.brand', 'product.media', 'product.variants']);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_key', $sessionKey);
        }

        $items = $query->orderBy('viewed_at', 'desc')->limit(8)->get();

        return response()->json($items);
    }

    /**
     * POST /api/v1/reviews
     */
    public function postReview(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
        ]);

        $userId = auth('sanctum')->id();
        $review = ProductReview::create([
            'product_id' => $request->product_id,
            'user_id' => $userId,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'status' => 'approved', // Auto approve for demo testing
            'is_verified' => true,
        ]);

        return response()->json(['review' => $review]);
    }
}
