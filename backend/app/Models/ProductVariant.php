<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'price',
        'sale_price',
        'cost_price',
        'weight',
        'length',
        'width',
        'height',
        'size_id',
        'color_id',
        'attributes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'attributes' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the parent product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the size.
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Get the color.
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Get the inventory items tracking this variant.
     */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'variant_id');
    }

    /**
     * Get aggregate available stock across warehouses.
     */
    public function getStockAttribute(): int
    {
        return $this->inventoryItems()->sum('quantity');
    }
}
