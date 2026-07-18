<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        FilamentAsset::register([
            Css::make('custom-filament-styles', asset('css/custom-filament.css')),
        ]);

        if (config('app.env') !== 'testing' && Schema::hasTable('settings')) {
            try {
                $smtpHost = \App\Models\Setting::get('mail_host') ?: \App\Models\Setting::get('smtp_host');
                if (!empty($smtpHost)) {
                    $decryptedPassword = '';
                    $encryptedPassword = \App\Models\Setting::get('mail_password') ?: \App\Models\Setting::get('smtp_password');
                    if (!empty($encryptedPassword)) {
                        try {
                            $decryptedPassword = decrypt($encryptedPassword);
                        } catch (\Exception $e) {}
                    }

                    config([
                        'mail.mailers.smtp.host' => $smtpHost,
                        'mail.mailers.smtp.port' => (int)(\App\Models\Setting::get('mail_port') ?: \App\Models\Setting::get('smtp_port', 465)),
                        'mail.mailers.smtp.username' => \App\Models\Setting::get('mail_username') ?: \App\Models\Setting::get('smtp_username'),
                        'mail.mailers.smtp.password' => $decryptedPassword,
                        'mail.mailers.smtp.encryption' => \App\Models\Setting::get('mail_encryption') ?: \App\Models\Setting::get('smtp_encryption', 'ssl'),
                        'mail.from.address' => \App\Models\Setting::get('mail_from_address') ?: \App\Models\Setting::get('smtp_from_email', 'noreply@superdollarsahiwal.com'),
                        'mail.from.name' => \App\Models\Setting::get('smtp_from_name') ?: \App\Models\Setting::get('site_name', 'AURA Enterprise'),
                    ]);
                }
            } catch (\Exception $e) {}
        }
    }
}
