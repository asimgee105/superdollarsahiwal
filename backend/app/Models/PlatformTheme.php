<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformTheme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'variables',
        'custom_css',
        'custom_js',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }
}
