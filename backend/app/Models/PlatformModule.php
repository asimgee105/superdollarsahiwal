<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformModule extends Model
{
    protected $fillable = [
        'name',
        'version',
        'description',
        'is_enabled',
        'dependencies',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'dependencies' => 'array',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }
}
