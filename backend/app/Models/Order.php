<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'status',

        // Shipping
        'shipping_name',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',

        // Billing
        'billing_name',
        'billing_phone',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',

        // Totals
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'total',

        // Payments
        'payment_method',
        'payment_status',

        // Configs
        'coupon_id',
        'gift_wrap',
        'gift_message',
        'order_notes',
    ];

    protected $casts = [
        'gift_wrap' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the customer user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coupon.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the items in the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the tracking timeline events.
     */
    public function timeline(): HasMany
    {
        return $this->hasMany(OrderTimeline::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the transactions list.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(OrderTransaction::class);
    }

    /**
     * Get return requests.
     */
    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }
}
