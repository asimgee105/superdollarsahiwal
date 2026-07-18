<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * GET /api/v1/orders
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        if (! $userId) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $orders = Order::where('user_id', $userId)
            ->with(['items.variant.product.media'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * GET /api/v1/orders/{order_number}
     */
    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.variant.product.media', 'timeline'])
            ->first();

        if (! $order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        return response()->json($order);
    }
}
