<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Color extends Model
{
    protected $fillable = [
        'name',
        'hex_code',
        'swatch_image',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the variants that carry this color.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'color_id');
    }
}
