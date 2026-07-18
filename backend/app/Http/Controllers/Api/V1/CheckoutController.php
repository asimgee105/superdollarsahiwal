<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    protected CartService $cartService;

    public function __construct(CheckoutService $checkoutService, CartService $cartService)
    {
        $this->checkoutService = $checkoutService;
        $this->cartService = $cartService;
    }

    /**
     * POST /api/v1/checkout
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'shipping' => 'required|array',
            'shipping.name' => 'required|string',
            'shipping.phone' => 'required|string',
            'shipping.address_line_1' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.state' => 'required|string',
            'shipping.postal_code' => 'required|string',
            'payment_method' => 'required|string|in:cod,stripe,paypal,razorpay,googlepay,applepay',
        ]);

        $userId = auth('sanctum')->id();
        $sessionKey = $request->header('X-Session-Key') ?: $request->input('session_key');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionKey);

        try {
            $order = $this->checkoutService->processCheckout(
                $cart,
                $request->shipping,
                $request->billing ?? $request->shipping,
                $request->payment_method,
                $request->notes
            );

            return response()->json([
                'message' => 'Order created successfully.',
                'order' => $order->load('items.variant.product', 'timeline'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
