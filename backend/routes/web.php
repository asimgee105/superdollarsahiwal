<?php

use App\Models\PlatformBackup;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/backup/download/{id}', function ($id) {
    $backup = PlatformBackup::findOrFail($id);

    return response()->streamDownload(function () {
        echo 'AURA Enterprise Commerce Platform Backup Archive Content Payload';
    }, $backup->filename);
})->name('backup.download');

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return 'Cache and configurations cleared successfully!';
});

/**
 * One-time setup route: runs migrate + seed, then returns full log.
 * Visit: http://localhost:8000/run-setup
 * After running, remove this route or it auto-disables in production.
 */
Route::get('/run-setup', function () {
    if (app()->isProduction()) {
        abort(403, 'Not allowed in production.');
    }

    $output = [];

    try {
        // 1. Run migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output[] = '✅ migrate: ' . trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $output[] = '❌ migrate error: ' . $e->getMessage();
    }

    try {
        // 2. Run seeder
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $output[] = '✅ db:seed: ' . trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $output[] = '❌ db:seed error: ' . $e->getMessage();
    }

    try {
        // 3. Clear all caches
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $output[] = '✅ All caches cleared.';
    } catch (\Throwable $e) {
        $output[] = '⚠️ Cache clear error: ' . $e->getMessage();
    }

    $html = '<html><head><meta charset="UTF-8"><title>AURA Setup</title>';
    $html .= '<style>body{font-family:monospace;background:#0f172a;color:#e2e8f0;padding:40px;line-height:1.8;}';
    $html .= 'h1{color:#ff3f6c;}pre{background:#1e293b;padding:20px;border-radius:8px;white-space:pre-wrap;}';
    $html .= 'a{color:#38bdf8;}.ok{color:#4ade80;}.err{color:#f87171;}</style></head><body>';
    $html .= '<h1>🚀 AURA Setup Runner</h1>';
    $html .= '<pre>' . implode("\n", $output) . '</pre>';
    $html .= '<br><p>✅ Setup complete! <a href="/admin">→ Go to Admin Panel</a></p>';
    $html .= '<p style="color:#94a3b8;font-size:12px;">Admin login: <strong>admin@aura.com</strong> / <strong>password</strong></p>';
    $html .= '</body></html>';

    return response($html);
});

/**
 * Re-seed only (skip migrate) — useful after first setup.
 * Visit: http://localhost:8000/run-seed
 */
Route::get('/run-seed', function () {
    if (app()->isProduction()) {
        abort(403, 'Not allowed in production.');
    }

    $output = [];

    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $output[] = '✅ db:seed: ' . trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $output[] = '❌ Error: ' . $e->getMessage();
        $output[] = $e->getTraceAsString();
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        $output[] = '✅ Cache cleared.';
    } catch (\Throwable $e) {
        $output[] = '⚠️ Cache error: ' . $e->getMessage();
    }

    $html = '<html><head><meta charset="UTF-8"><title>AURA Seed</title>';
    $html .= '<style>body{font-family:monospace;background:#0f172a;color:#e2e8f0;padding:40px;line-height:1.8;}';
    $html .= 'h1{color:#ff3f6c;}pre{background:#1e293b;padding:20px;border-radius:8px;white-space:pre-wrap;}</style></head><body>';
    $html .= '<h1>🌱 AURA Seeder Runner</h1>';
    $html .= '<pre>' . implode("\n", $output) . '</pre>';
    $html .= '<br><p><a href="/admin" style="color:#38bdf8;">→ Go to Admin Panel</a></p>';
    $html .= '<p style="color:#94a3b8;font-size:12px;">Admin: <strong>admin@aura.com</strong> / <strong>password</strong></p>';
    $html .= '</body></html>';

    return response($html);
});
