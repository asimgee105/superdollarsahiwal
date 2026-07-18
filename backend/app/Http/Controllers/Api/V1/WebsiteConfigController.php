<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HomepageLayout;
use App\Models\HomepageSection;
use App\Models\NavigationMenu;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class WebsiteConfigController extends Controller
{
    /**
     * Helper to retrieve all system settings.
     */
    private function getSettings(): array
    {
        if (request()->has('nocache')) {
            $settings = Setting::pluck('value', 'key')->toArray();
            foreach ($settings as $key => $value) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $settings[$key] = $decoded;
                }
            }
            return $settings;
        }

        return Cache::remember('settings', 15, function () {
            $settings = Setting::pluck('value', 'key')->toArray();
            foreach ($settings as $key => $value) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $settings[$key] = $decoded;
                }
            }

            return $settings;
        });
    }

    /**
     * GET /api/v1/settings
     */
    public function settings(): JsonResponse
    {
        $settings = $this->getSettings();

        return response()->json([
            'site_name' => $settings['site_name'] ?? 'Myntra Headless',
            'site_tagline' => $settings['site_tagline'] ?? 'Shop Premium Clothing',
            'logo_url' => $settings['logo_url'] ?? '',
            'favicon_url' => $settings['favicon_url'] ?? '',
            'logo_light_url' => $settings['logo_light_url'] ?? '',
            'logo_dark_url' => $settings['logo_dark_url'] ?? '',
            'currency' => $settings['currency'] ?? 'Rs',
            'timezone' => $settings['timezone'] ?? 'Asia/Karachi',
            'language' => $settings['language'] ?? 'en',
            'dark_mode_ready' => (bool) ($settings['dark_mode_ready'] ?? true),
            'auth_providers' => [
                'email_password' => (bool) ($settings['auth_email_password_enabled'] ?? true),
                'email_otp' => (bool) ($settings['auth_email_otp_enabled'] ?? true),
                'mobile_password' => (bool) ($settings['auth_mobile_password_enabled'] ?? true),
                'mobile_otp' => (bool) ($settings['auth_mobile_otp_enabled'] ?? true),
                'google' => (bool) ($settings['oauth_google_enabled'] ?? false),
                'facebook' => (bool) ($settings['oauth_facebook_enabled'] ?? false),
                'apple' => (bool) ($settings['oauth_apple_enabled'] ?? false),
                'github' => (bool) ($settings['oauth_github_enabled'] ?? false),
                'microsoft' => (bool) ($settings['oauth_microsoft_enabled'] ?? false),
                'linkedin' => (bool) ($settings['oauth_linkedin_enabled'] ?? false),
                'twitter' => (bool) ($settings['oauth_twitter_enabled'] ?? false),
                'phone_login' => (bool) ($settings['phone_login_enabled'] ?? false),
            ],
            'otp_config' => [
                'length' => (int) ($settings['otp_length'] ?? 6),
                'expiry_minutes' => (int) ($settings['otp_expiry_minutes'] ?? 10),
                'resend_delay_seconds' => (int) ($settings['otp_resend_delay_seconds'] ?? 60),
            ],
            'payment_gateways' => [
                'cod' => (bool) ($settings['payment_cod_enabled'] ?? true),
                'stripe' => (bool) ($settings['payment_stripe_enabled'] ?? false),
                'googlepay' => (bool) ($settings['payment_googlepay_enabled'] ?? false),
                'paypal' => (bool) ($settings['payment_paypal_enabled'] ?? false),
                'applepay' => (bool) ($settings['payment_applepay_enabled'] ?? false),
            ],
            'contact' => [
                'phone' => $settings['contact_phone'] ?? '080-40011450',
                'email' => $settings['contact_email'] ?? 'support@myntra.com',
                'address' => $settings['contact_address'] ?? 'Buildings Alyssa, Bengaluru, India',
            ],
            'socials' => [
                'facebook' => $settings['social_facebook'] ?? '#',
                'twitter' => $settings['social_twitter'] ?? '#',
                'youtube' => $settings['social_youtube'] ?? '#',
                'instagram' => $settings['social_instagram'] ?? '#',
            ],
            'integrations' => [
                'google_analytics_id' => $settings['google_analytics_id'] ?? '',
                'google_tag_manager_id' => $settings['google_tag_manager_id'] ?? '',
                'facebook_pixel_id' => $settings['facebook_pixel_id'] ?? '',
            ],
            'custom_css' => $settings['custom_css'] ?? '',
            'custom_js' => $settings['custom_js'] ?? '',
        ]);
    }

    /**
     * GET /api/v1/theme
     */
    public function theme(): JsonResponse
    {
        $settings = $this->getSettings();

        return response()->json([
            'colors' => [
                'primary' => $settings['primary_color'] ?? '#ff3f6c',
                'secondary' => $settings['secondary_color'] ?? '#1a1a1a',
                'success' => $settings['success_color'] ?? '#10b981',
                'warning' => $settings['warning_color'] ?? '#f59e0b',
                'error' => $settings['error_color'] ?? '#ef4444',
            ],
            'typography' => [
                'font_family' => $settings['typography_font'] ?? 'Outfit',
            ],
            'styling' => [
                'button_style' => $settings['button_style'] ?? 'rounded',
                'border_radius' => $settings['border_radius'] ?? '4px',
                'container_width' => $settings['container_width'] ?? '1280px',
            ],
        ]);
    }

    /**
     * GET /api/v1/header
     */
    public function header(): JsonResponse
    {
        $settings = $this->getSettings();
        $navigation = $this->getNavigationArray();

        return response()->json([
            'announcement_bar' => $settings['header_announcement_bar'] ?? 'FLAT $30.00 OFF + FREE SHIPPING',
            'sticky_header' => (bool) ($settings['sticky_header'] ?? true),
            'transparent_header' => (bool) ($settings['transparent_header'] ?? false),
            'logo_url' => $settings['logo_url'] ?? '',
            'navigation' => $navigation,
        ]);
    }

    /**
     * GET /api/v1/footer
     */
    public function footer(): JsonResponse
    {
        $settings = $this->getSettings();

        return response()->json([
            'contact' => [
                'phone' => $settings['footer_phone'] ?? $settings['contact_phone'] ?? '0300-1234567',
                'email' => $settings['footer_email'] ?? $settings['contact_email'] ?? 'support@aura.com',
                'address' => $settings['footer_address'] ?? $settings['contact_address'] ?? 'Gulberg, Lahore, Pakistan',
            ],
            'socials' => [
                'facebook' => $settings['social_facebook'] ?? '#',
                'twitter' => $settings['social_twitter'] ?? '#',
                'youtube' => $settings['social_youtube'] ?? '#',
                'instagram' => $settings['social_instagram'] ?? '#',
            ],
            'copyright' => $settings['footer_copyright'] ?? '© 2026 www.myntra.com. All rights reserved.',
            'about_text' => $settings['footer_about_text'] ?? 'AURA is a premium high-fidelity enterprise eCommerce suite.',
            'popular_searches' => $settings['footer_popular_searches'] ?? 'Makeup | Dresses For Girls | T-Shirts | Sandals | Bags | Sport Shoes',
            'col1_title' => $settings['footer_col1_title'] ?? 'ONLINE SHOPPING',
            'col1_links' => $settings['footer_col1_links'] ?? "Men\nWomen\nKids\nHome & Living\nBeauty\nGenz",
            'col2_title' => $settings['footer_col2_title'] ?? 'CUSTOMER POLICIES',
            'col2_links' => $settings['footer_col2_links'] ?? "Contact Us\nFAQ\nT&C\nTrack Orders\nShipping\nPrivacy Policy",
        ]);
    }

    /**
     * GET /api/v1/navigation
     */
    public function navigation(): JsonResponse
    {
        return response()->json($this->getNavigationArray());
    }

    /**
     * GET /api/v1/homepage
     */
    public function homepage(): JsonResponse
    {
        $settings = $this->getSettings();

        $data = Cache::rememberForever('homepage_data', function () use ($settings) {
            $layoutId = $settings['active_homepage_layout'] ?? null;

            if ($layoutId) {
                $layout = HomepageLayout::find($layoutId);
            } else {
                $layout = HomepageLayout::where('is_active', true)->first();
            }

            if (! $layout) {
                return ['error' => 'No active layout profile.', 'status' => 404];
            }

            // Fetch scheduled and active sections
            $now = now();
            $sections = HomepageSection::where('layout_id', $layout->id)
                ->where('is_enabled', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                })
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($section) => [
                    'id' => $section->id,
                    'key' => $section->section_key,
                    'title' => $section->title,
                    'subtitle' => $section->subtitle,
                    'description' => $section->description,
                    'background' => [
                        'type' => $section->background_type,
                        'color' => $section->background_color,
                        'image' => $section->background_image,
                        'video' => $section->background_video,
                    ],
                    'padding' => $section->padding,
                    'margin' => $section->margin,
                    'width' => $section->width,
                    'animation' => $section->animation,
                    'button_text' => $section->button_text,
                    'button_url' => $section->button_url,
                    'layout_variation' => $section->layout_variation,
                    'show_on_mobile' => (bool) $section->show_on_mobile,
                    'show_on_desktop' => (bool) $section->show_on_desktop,
                    'settings' => $section->settings,
                ])
                ->toArray();

            return [
                'layout_name' => $layout->name,
                'header_style' => $layout->header_style,
                'hero_style' => $layout->hero_style,
                'category_style' => $layout->category_style,
                'product_card_style' => $layout->product_card_style,
                'footer_style' => $layout->footer_style,
                'sections' => $sections,
            ];
        });

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], $data['status'] ?? 404);
        }

        return response()->json($data);
    }

    /**
     * Helper to compile navigation menu hierarchy.
     */
    private function getNavigationArray(): array
    {
        return Cache::rememberForever('navigation_hierarchy', function () {
            $menu = NavigationMenu::where('key', 'main_header')->where('is_active', true)->first();
            if (! $menu) {
                return \App\Models\Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereHas('products')
                            ->orWhereHas('children.products')
                            ->orWhereHas('children.children.products');
                    })
                    ->orderBy('sort_order')
                    ->with(['children' => function ($q) {
                        $q->where('is_active', true)
                            ->where(function ($sq) {
                                $sq->whereHas('products')
                                    ->orWhereHas('children.products');
                            })
                            ->with(['children' => function ($sq) {
                                $sq->where('is_active', true)->whereHas('products');
                            }]);
                    }])
                    ->get()
                    ->map(fn ($cat) => [
                        'title' => $cat->name,
                        'url' => '/catalog/?category=' . $cat->slug,
                        'type' => 'category',
                        'children' => $cat->children->map(fn ($child) => [
                            'title' => $child->name,
                            'url' => '/catalog/?category=' . $child->slug,
                            'type' => 'category',
                            'items' => $child->children->map(fn ($subChild) => [
                                'title' => $subChild->name,
                                'url' => '/catalog/?category=' . $subChild->slug,
                            ])->toArray(),
                        ])->toArray(),
                    ])->toArray();
            }

            return $menu->items()
                ->with('children')
                ->get()
                ->map(fn ($item) => [
                    'title' => $item->title,
                    'url' => $item->url,
                    'type' => $item->type,
                    'children' => $item->children->map(fn ($child) => [
                        'title' => $child->title,
                        'url' => $child->url,
                        'type' => $child->type,
                    ])->toArray(),
                ])->toArray();
        });
    }

    /**
     * GET /api/v1/page/{slug}
     */
    public function getPage(string $slug): JsonResponse
    {
        $settings = $this->getSettings();
        $key = 'page_' . str_replace('-', '_', $slug);

        $content = $settings[$key] ?? null;

        if (!$content) {
            return response()->json([
                'title' => ucwords(str_replace('-', ' ', $slug)),
                'content' => "## " . ucwords(str_replace('-', ' ', $slug)) . "\n\nThis page is currently being updated by the administration. Please check back later."
            ]);
        }

        return response()->json([
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'content' => $content
        ]);
    }
}
