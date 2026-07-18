<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ReturnRequest extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'type',
        'reason',
        'customer_notes',
        'admin_notes',
        'media_paths',
        'status',
        'pickup_status',
        'refund_amount',
    ];

    protected $casts = [
        'media_paths' => 'array',
        'refund_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the order associated with this return.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the exact item requested for return.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
