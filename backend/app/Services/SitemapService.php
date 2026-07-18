<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;

class SitemapService
{
    /**
     * Generate dynamic XML sitemap payload.
     */
    public function generateSitemap(): string
    {
        $appUrl = env('APP_URL', 'http://localhost');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // 1. Static Homepage
        $xml .= '<url><loc>'.$appUrl.'</loc><priority>1.0</priority></url>';

        // 2. Products
        $products = Product::where('is_active', true)->get();
        foreach ($products as $p) {
            $xml .= '<url><loc>'.$appUrl.'/product/'.$p->slug.'</loc><priority>0.8</priority></url>';
        }

        // 3. Categories
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $c) {
            $xml .= '<url><loc>'.$appUrl.'/catalog?category='.$c->slug.'</loc><priority>0.7</priority></url>';
        }

        // 4. CMS Posts
        $posts = Post::where('is_published', true)->get();
        foreach ($posts as $post) {
            $xml .= '<url><loc>'.$appUrl.'/blog/'.$post->slug.'</loc><priority>0.6</priority></url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
