<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * GET /api/v1/cart
     */
    public function show(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $sessionKey = $request->header('X-Session-Key') ?: $request->input('session_key');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionKey);
        $totals = $this->cartService->getTotals($cart);

        return response()->json([
            'cart' => $cart->load('items.variant.product.media', 'items.variant.size', 'items.variant.color'),
            'totals' => $totals,
        ]);
    }

    /**
     * POST /api/v1/cart/items
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = auth('sanctum')->id();
        $sessionKey = $request->header('X-Session-Key') ?: $request->input('session_key');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionKey);

        try {
            $item = $this->cartService->addItem($cart, $request->variant_id, $request->quantity);
            $totals = $this->cartService->getTotals($cart);

            return response()->json(['item' => $item, 'totals' => $totals, 'message' => 'Item added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * PUT /api/v1/cart/items/{variant_id}
     */
    public function updateItem(Request $request, int $variantId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = auth('sanctum')->id();
        $sessionKey = $request->header('X-Session-Key') ?: $request->input('session_key');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionKey);

        try {
            $item = $this->cartService->updateItem($cart, $variantId, $request->quantity);
            $totals = $this->cartService->getTotals($cart);

            return response()->json(['item' => $item, 'totals' => $totals, 'message' => 'Cart updated.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * DELETE /api/v1/cart/items/{variant_id}
     */
    public function removeItem(Request $request, int $variantId): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $sessionKey = $request->header('X-Session-Key') ?: $request->input('session_key');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionKey);

        $this->cartService->removeItem($cart, $variantId);
        $totals = $this->cartService->getTotals($cart);

        return response()->json(['totals' => $totals, 'message' => 'Item removed.']);
    }

    /**
     * POST /api/v1/cart/coupon
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $userId = auth('sanctum')->id();
        $sessionKey = $request->header('X-Session-Key') ?: $request->input('session_key');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionKey);

        try {
            $this->cartService->applyCoupon($cart, $request->coupon_code);
            $totals = $this->cartService->getTotals($cart);

            return response()->json(['totals' => $totals, 'message' => 'Coupon applied successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * POST /api/v1/cart/merge
     */
    public function merge(Request $request): JsonResponse
    {
        $request->validate([
            'session_key' => 'required|string',
        ]);

        $userId = auth('sanctum')->id();
        if (! $userId) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $cart = $this->cartService->mergeCarts($userId, $request->session_key);
        $totals = $this->cartService->getTotals($cart);

        return response()->json(['cart' => $cart->load('items.variant'), 'totals' => $totals]);
    }
}
