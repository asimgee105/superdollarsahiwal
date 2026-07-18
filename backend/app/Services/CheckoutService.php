<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimeline;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Process checkout pipeline.
     */
    public function processCheckout(
        Cart $cart,
        array $shipping,
        array $billing,
        string $paymentMethod = 'cod',
        ?string $notes = null
    ): Order {
        if ($cart->items()->count() === 0) {
            throw new \Exception('Cannot process checkout for an empty cart.');
        }

        // Get fresh recalculated totals
        $totals = $this->cartService->getTotals($cart);

        return DB::transaction(function () use ($cart, $shipping, $billing, $paymentMethod, $notes, $totals) {
            // 1. Resolve Coupon Usage limit increase
            $couponId = null;
            if ($cart->coupon_code) {
                $coupon = Coupon::where('code', $cart->coupon_code)->lockForUpdate()->first();
                if ($coupon) {
                    $couponId = $coupon->id;
                    $coupon->increment('used_count');
                }
            }

            // 2. Generate unique order number
            $orderNumber = 'ORD-'.strtoupper(Str::random(10));

            // 3. Create core Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $cart->user_id,
                'status' => 'pending',

                // Shipping
                'shipping_name' => $shipping['name'],
                'shipping_phone' => $shipping['phone'],
                'shipping_address_line_1' => $shipping['address_line_1'],
                'shipping_address_line_2' => $shipping['address_line_2'] ?? null,
                'shipping_city' => $shipping['city'],
                'shipping_state' => $shipping['state'],
                'shipping_postal_code' => $shipping['postal_code'],
                'shipping_country' => $shipping['country'] ?? 'Pakistan',

                // Billing
                'billing_name' => $billing['name'] ?? $shipping['name'],
                'billing_phone' => $billing['phone'] ?? $shipping['phone'],
                'billing_address_line_1' => $billing['address_line_1'] ?? $shipping['address_line_1'],
                'billing_address_line_2' => $billing['address_line_2'] ?? ($shipping['address_line_2'] ?? null),
                'billing_city' => $billing['city'] ?? $shipping['city'],
                'billing_state' => $billing['state'] ?? $shipping['state'],
                'billing_postal_code' => $billing['postal_code'] ?? $shipping['postal_code'],
                'billing_country' => $billing['country'] ?? ($shipping['country'] ?? 'Pakistan'),

                // Breakdown
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount'],
                'tax_amount' => $totals['tax'],
                'shipping_cost' => $totals['shipping'],
                'total' => $totals['total'],

                // Payment
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'paid',

                'coupon_id' => $couponId,
                'gift_wrap' => $cart->gift_wrap,
                'gift_message' => $cart->gift_message,
                'order_notes' => $notes,
            ]);

            // 4. Copy Cart items to Order items & Adjust physical stock levels
            foreach ($cart->items as $item) {
                $price = $item->variant->sale_price ?? $item->variant->price;
                $itemTotal = ($price * $item->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $item->variant_id,
                    'sku' => $item->variant->sku,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'discount' => 0.00,
                    'total' => $itemTotal,
                ]);

                // Reduce Inventory stock (Karachi Main Hub)
                $inventory = InventoryItem::where('variant_id', $item->variant_id)->first();
                if ($inventory) {
                    if ($inventory->quantity < $item->quantity) {
                        throw new \Exception('Variant SKU: '.$item->variant->sku.' went out of stock during checkout.');
                    }
                    $inventory->decrement('quantity', $item->quantity);

                    // Add adjustment log record
                    InventoryLog::create([
                        'inventory_item_id' => $inventory->id,
                        'type' => 'sale',
                        'quantity_changed' => -$item->quantity,
                        'reference' => 'Checkout: '.$orderNumber,
                    ]);
                }
            }

            // 5. Initial status tracking Timeline logging
            OrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'title' => 'Order Placed Successfully',
                'description' => 'The order has been created and is awaiting processing confirmation.',
            ]);

            // 6. Log transaction ledger records
            OrderTransaction::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $order->total,
                'status' => $paymentMethod === 'cod' ? 'pending' : 'success',
                'request_payload' => ['order_number' => $orderNumber],
                'response_payload' => ['payment_method' => $paymentMethod, 'status' => 'success'],
            ]);

            // 7. Write central template notifications log
            NotificationLog::create([
                'user_id' => $order->user_id,
                'channel' => 'email',
                'recipient' => $shipping['phone'].' / '.($order->user->email ?? 'guest@fashiondomain.local'),
                'subject' => 'Order Confirmed: '.$orderNumber,
                'body' => 'Thank you for placing order '.$orderNumber.' at AURA. Total Rs. '.$order->total.'.',
                'status' => 'sent',
            ]);

            // 8. Flush cart items
            $cart->items()->delete();
            $cart->update(['coupon_code' => null, 'gift_wrap' => false, 'gift_message' => null]);

            return $order;
        });
    }
}
