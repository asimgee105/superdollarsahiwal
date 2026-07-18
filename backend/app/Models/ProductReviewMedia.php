<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReviewMedia extends Model
{
    protected $table = 'product_review_media';

    protected $fillable = [
        'review_id',
        'path',
        'type',
    ];

    /**
     * Get the review record.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class, 'review_id');
    }
}
