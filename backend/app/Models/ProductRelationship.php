<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRelationship extends Model
{
    protected $table = 'product_relationships';

    protected $fillable = [
        'product_id',
        'related_id',
        'type',
    ];

    /**
     * Get the base product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the related product.
     */
    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_id');
    }
}
