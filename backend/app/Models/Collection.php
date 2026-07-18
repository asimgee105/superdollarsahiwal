<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_url',
        'is_active',
        'is_featured',
        'is_trending',
        'season',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the products in this collection.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_collection');
    }
}
