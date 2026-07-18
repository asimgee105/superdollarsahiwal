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
