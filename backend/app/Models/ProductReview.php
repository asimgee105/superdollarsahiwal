<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'comment',
        'status',
        'is_verified',
        'helpful_votes',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the reviewer user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get media attachments for this review.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductReviewMedia::class, 'review_id');
    }

    /**
     * Get dynamic replies on this review.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ProductReviewReply::class, 'review_id');
    }
}
