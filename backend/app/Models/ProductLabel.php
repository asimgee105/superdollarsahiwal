<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class ProductLabel extends Model
{
    protected $fillable = [
        'name',
        'bg_color',
        'text_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get products carrying this label.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'label_id');
    }
}
