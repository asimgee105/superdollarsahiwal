<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand_id',
        'label_id',
        'title',
        'slug',
        'sku',
        'barcode',
        'type',
        'description',
        'short_description',
        'highlights',
        'specifications',
        'wash_care',
        'origin_country',
        'is_active',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'canonical_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'highlights' => 'array',
        'specifications' => 'array',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the brand.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the label.
     */
    public function label(): BelongsTo
    {
        return $this->belongsTo(ProductLabel::class, 'label_id');
    }

    /**
     * Get the categories.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    /**
     * Get the collections.
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'product_collection');
    }

    /**
     * Get the media gallery.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class, 'product_id')->orderBy('sort_order');
    }

    /**
     * Get the product variants.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    /**
     * Get product reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    /**
     * Helper to load related upsells.
     */
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_relationships', 'product_id', 'related_id')
            ->wherePivot('type', 'related');
    }

    public function upsells(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_relationships', 'product_id', 'related_id')
            ->wherePivot('type', 'upsell');
    }

    public function crosssells(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_relationships', 'product_id', 'related_id')
            ->wherePivot('type', 'cross-sell');
    }

    public function boughtTogether(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_relationships', 'product_id', 'related_id')
            ->wherePivot('type', 'bought-together');
    }
}
