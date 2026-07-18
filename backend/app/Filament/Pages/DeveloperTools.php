<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class DeveloperTools extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $title = 'Developer Tools';

    protected static ?string $navigationGroup = 'Platform Core';

    protected static string $view = 'filament.pages.developer-tools';

    /**
     * Clear Cache Artisan Action trigger.
     */
    public function clearCache(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        Notification::make()
            ->title('All System Cache Flags Flushed!')
            ->success()
            ->send();
    }

    /**
     * Delete all dummy catalog & sales data safely.
     */
    public function deleteDummyData(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'brands', 'categories', 'collections', 'homepage_layouts', 'homepage_sections',
            'sizes', 'colors', 'product_labels', 'products', 'product_variants',
            'product_media', 'product_category', 'product_collection', 'product_reviews',
            'orders', 'order_items', 'order_transactions', 'order_timeline', 'coupons',
            'posts', 'post_categories', 'post_category_pivot', 'faqs', 'testimonials',
            'lookbooks', 'warehouses', 'inventory_items', 'return_requests', 'addresses'
        ];

        foreach ($tables as $table) {
            \Illuminate\Support\Facades\DB::table($table)->truncate();
        }

        // Keep admin and manager users, delete customer users
        $adminEmails = ['admin@superdollarsahiwal.com', 'admin1@example.com', 'admin2@example.com'];
        $customerUserIds = \Illuminate\Support\Facades\DB::table('users')
            ->whereNotIn('email', $adminEmails)
            ->where('email', 'not like', 'manager%@example.com')
            ->pluck('id')
            ->toArray();

        if (!empty($customerUserIds)) {
            \Illuminate\Support\Facades\DB::table('users')->whereIn('id', $customerUserIds)->delete();
            \Illuminate\Support\Facades\DB::table('user_profiles')->whereIn('user_id', $customerUserIds)->delete();
            \Illuminate\Support\Facades\DB::table('model_has_roles')->whereIn('model_id', $customerUserIds)->where('model_type', 'App\Models\User')->delete();
        }

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Flush caches
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        Notification::make()
            ->title('All Dummy Data Deleted successfully!')
            ->success()
            ->send();
    }

    /**
     * Re-seed all dummy catalog & sales data.
     */
    public function generateDummyData(): void
    {
        Artisan::call('db:seed');
        
        // Flush caches
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        Notification::make()
            ->title('Dummy Data Generated successfully!')
            ->success()
            ->send();
    }
}
