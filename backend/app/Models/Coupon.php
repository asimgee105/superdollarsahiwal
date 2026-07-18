<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_cart_value',
        'max_discount',
        'usage_limit',
        'usage_per_user',
        'used_count',
        'priority',
        'is_active',
        'starts_at',
        'expires_at',
        'applicable_categories',
        'applicable_brands',
        'applicable_products',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_cart_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'applicable_categories' => 'array',
        'applicable_brands' => 'array',
        'applicable_products' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Helper to verify if coupon has expired.
     */
    public function isExpired(): bool
    {
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return true;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return true;
        }

        return false;
    }

    /**
     * Helper to verify limits.
     */
    public function isLimitReached(): bool
    {
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return true;
        }

        return false;
    }
}
