<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'type',
        'quantity_changed',
        'reference',
    ];

    /**
     * Get the associated inventory item record.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
