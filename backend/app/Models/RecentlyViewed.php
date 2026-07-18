<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentlyViewed extends Model
{
    protected $table = 'recently_viewed';

    protected $fillable = [
        'user_id',
        'session_key',
        'product_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the associated product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
