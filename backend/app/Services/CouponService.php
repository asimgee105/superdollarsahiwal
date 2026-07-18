<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;

class CouponService
{
    /**
     * Validates coupon eligibility against cart categories, brands, and totals.
     */
    public function validateCoupon(string $code, Cart $cart): Coupon
    {
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();
        if (! $coupon) {
            throw new \Exception('Coupon code does not exist.');
        }

        if ($coupon->isExpired()) {
            throw new \Exception('Coupon is expired.');
        }

        if ($coupon->isLimitReached()) {
            throw new \Exception('Coupon limit reached.');
        }

        // Calculate cart subtotal
        $subtotal = 0.00;
        foreach ($cart->items as $item) {
            $price = $item->variant->sale_price ?? $item->variant->price;
            $subtotal += ($price * $item->quantity);
        }

        if ($subtotal < $coupon->min_cart_value) {
            throw new \Exception('Minimum cart value of Rs. '.$coupon->min_cart_value.' is required.');
        }

        return $coupon;
    }
}
