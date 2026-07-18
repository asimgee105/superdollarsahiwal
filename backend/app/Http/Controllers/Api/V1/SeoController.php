<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SitemapService;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    protected SitemapService $sitemapService;

    public function __construct(SitemapService $sitemapService)
    {
        $this->sitemapService = $sitemapService;
    }

    /**
     * GET /sitemap.xml
     */
    public function sitemap(): Response
    {
        $content = $this->sitemapService->generateSitemap();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * GET /robots.txt
     */
    public function robots(): Response
    {
        $appUrl = env('APP_URL', 'http://localhost');
        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        $content .= "Sitemap: {$appUrl}/sitemap.xml\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
