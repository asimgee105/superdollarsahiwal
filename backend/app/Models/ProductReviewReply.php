<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReviewReply extends Model
{
    protected $table = 'product_review_replies';

    protected $fillable = [
        'review_id',
        'user_id',
        'reply',
    ];

    /**
     * Get the review record.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class, 'review_id');
    }

    /**
     * Get the replying user details.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
