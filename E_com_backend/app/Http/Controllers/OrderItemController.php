<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    /**
     * Display all items belonging to one of the user's orders.
     */
    public function index(Request $request, string $orderId)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $items = $order->items()
            ->with('product')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Order items retrieved successfully.',
            'data' => $items,
        ], 200);
    }

    /**
     * Display one item belonging to one of the user's orders.
     */
    public function show(
        Request $request,
        string $orderId,
        string $itemId
    ) {
        $order = Order::where('user_id', $request->user()->id)
            ->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $item = $order->items()
            ->with('product')
            ->find($itemId);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order item retrieved successfully.',
            'data' => $item,
        ], 200);
    }
}