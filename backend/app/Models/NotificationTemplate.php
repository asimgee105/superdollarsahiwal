<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'channels',
        'is_active',
    ];

    protected $casts = [
        'channels' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }
}
