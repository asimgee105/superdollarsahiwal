<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Lookbook;
use App\Models\Post;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class CmsController extends Controller
{
    /**
     * GET /api/v1/posts
     */
    public function posts(): JsonResponse
    {
        $posts = Post::where('is_published', true)
            ->with(['author', 'categories'])
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }

    /**
     * GET /api/v1/posts/{slug}
     */
    public function post(string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->with(['author', 'categories'])
            ->first();

        if (! $post) {
            return response()->json(['error' => 'Article not found.'], 404);
        }

        return response()->json($post);
    }

    /**
     * GET /api/v1/faqs
     */
    public function faqs(): JsonResponse
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($faqs);
    }

    /**
     * GET /api/v1/testimonials
     */
    public function testimonials(): JsonResponse
    {
        $testimonials = Testimonial::where('is_active', true)
            ->latest()
            ->get();

        return response()->json($testimonials);
    }

    /**
     * GET /api/v1/lookbooks
     */
    public function lookbooks(): JsonResponse
    {
        $lookbooks = Lookbook::where('is_active', true)
            ->latest()
            ->get();

        return response()->json($lookbooks);
    }
}
