<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Lookbook extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image_url',
        'description',
        'tagged_product_ids',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tagged_product_ids' => 'array',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }
}
