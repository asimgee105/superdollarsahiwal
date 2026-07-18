<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'amount',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
