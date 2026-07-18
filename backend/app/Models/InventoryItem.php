<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class InventoryItem extends Model
{
    protected $fillable = [
        'warehouse_id',
        'variant_id',
        'quantity',
        'reserved',
        'incoming',
        'damaged',
        'returned',
        'low_stock_threshold',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the warehouse location.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get movement logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }
}
