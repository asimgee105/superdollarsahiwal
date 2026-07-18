<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Size extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'size_chart',
    ];

    protected $casts = [
        'size_chart' => 'array',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the variants that carry this size.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }
}
