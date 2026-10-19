<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display the authenticated user's orders.
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully.',
            'data' => $orders,
        ], 200);
    }

    /**
     * Create an order from the authenticated user's cart.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'max:1000'],
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            $cart = Cart::where('user_id', $request->user()->id)
                ->with('items')
                ->lockForUpdate()
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['Your cart is empty.'],
                ]);
            }

            $lineItems = [];
            $total = 0;

            // Verify stock and calculate prices from the database.
            foreach ($cart->items as $cartItem) {
                $product = Product::whereKey($cartItem->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($cartItem->quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => [
                            "Insufficient stock for product: {$product->name}.",
                        ],
                    ]);
                }

                $price = (float) $product->price;

                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $cartItem->quantity,
                    'price' => $price,
                ];

                $total += $price * $cartItem->quantity;
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => round($total, 2),
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($lineItems as $lineItem) {
                $product = $lineItem['product'];
                $quantity = $lineItem['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $lineItem['price'],
                ]);

                // Reserve the ordered stock.
                $product->decrement('stock', $quantity);
            }

            // Empty the cart only after the order has been created.
            $cart->items()->delete();

            return $order;
        });

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }

    /**
     * Display one of the authenticated user's orders.
     */
    public function show(Request $request, string $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully.',
            'data' => $order,
        ], 200);
    }
}