<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'session_key',
        'product_id',
    ];

    /**
     * Get the associated product details.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
