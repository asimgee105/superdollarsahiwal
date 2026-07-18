<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Get or create a cart for auth user or guest.
     */
    public function getOrCreateCart(?int $userId, ?string $sessionKey): Cart
    {
        if ($userId) {
            return Cart::firstOrCreate(['user_id' => $userId]);
        }

        return Cart::firstOrCreate(['session_key' => $sessionKey]);
    }

    /**
     * Add variant item to cart with stock checks.
     */
    public function addItem(Cart $cart, int $variantId, int $qty): CartItem
    {
        $variant = ProductVariant::findOrFail($variantId);

        // Reserved Stock check
        $availableStock = $variant->stock;
        if ($availableStock < $qty) {
            throw new \Exception('Requested quantity exceeds available stock ('.$availableStock.' left).');
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $variantId)
            ->first();

        if ($item) {
            $newQty = $item->quantity + $qty;
            if ($availableStock < $newQty) {
                throw new \Exception('Exceeds available stock on addition ('.$availableStock.' left).');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'variant_id' => $variantId,
                'quantity' => $qty,
            ]);
        }

        return $item;
    }

    /**
     * Update quantity.
     */
    public function updateItem(Cart $cart, int $variantId, int $qty): CartItem
    {
        $variant = ProductVariant::findOrFail($variantId);
        if ($variant->stock < $qty) {
            throw new \Exception('Exceeds available stock level.');
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $variantId)
            ->firstOrFail();

        $item->update(['quantity' => $qty]);

        return $item;
    }

    /**
     * Remove item.
     */
    public function removeItem(Cart $cart, int $variantId): void
    {
        CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $variantId)
            ->delete();
    }

    /**
     * Merge guest cart into customer cart after login.
     */
    public function mergeCarts(int $userId, string $sessionKey): Cart
    {
        $customerCart = Cart::firstOrCreate(['user_id' => $userId]);
        $guestCart = Cart::where('session_key', $sessionKey)->first();

        if ($guestCart) {
            DB::transaction(function () use ($customerCart, $guestCart) {
                foreach ($guestCart->items as $gItem) {
                    $cItem = CartItem::where('cart_id', $customerCart->id)
                        ->where('variant_id', $gItem->variant_id)
                        ->first();

                    if ($cItem) {
                        $cItem->update(['quantity' => $cItem->quantity + $gItem->quantity]);
                    } else {
                        $gItem->update(['cart_id' => $customerCart->id]);
                    }
                }

                // Copy coupon if customer has none
                if (! $customerCart->coupon_code && $guestCart->coupon_code) {
                    $customerCart->update(['coupon_code' => $guestCart->coupon_code]);
                }

                $guestCart->delete();
            });
        }

        return $customerCart;
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(Cart $cart, string $couponCode): void
    {
        $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
        if (! $coupon) {
            throw new \Exception('Invalid coupon code.');
        }

        if ($coupon->isExpired()) {
            throw new \Exception('This coupon has expired.');
        }

        if ($coupon->isLimitReached()) {
            throw new \Exception('This coupon limit has been reached.');
        }

        $cart->update(['coupon_code' => $couponCode]);
    }

    /**
     * Recalculates financial totals (Subtotal, Discount, Tax 5%, Shipping, Grand Total).
     */
    public function getTotals(Cart $cart): array
    {
        $subtotal = 0.00;
        $items = $cart->items()->with('variant')->get();

        foreach ($items as $item) {
            $price = $item->variant->sale_price ?? $item->variant->price;
            $subtotal += ($price * $item->quantity);
        }

        $discount = 0.00;
        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon && $subtotal >= $coupon->min_cart_value) {
                if ($coupon->type === 'percentage') {
                    $discount = ($subtotal * ($coupon->value / 100));
                    if ($coupon->max_discount && $discount > $coupon->max_discount) {
                        $discount = $coupon->max_discount;
                    }
                } elseif ($coupon->type === 'flat') {
                    $discount = $coupon->value;
                }
            }
        }

        $shipping = $subtotal > 0 && $subtotal < 3000 ? 150.00 : 0.00; // Free shipping above 3000
        $tax = ($subtotal - $discount) * 0.05; // 5% VAT tax
        $total = ($subtotal - $discount) + $tax + $shipping;

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
        ];
    }
}
