<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Display the authenticated user's cart.
     */
    public function index(Request $request)
    {
        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $cart->load('items.product');

        $total = $cart->items->sum(function ($item) {
            return (float) $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully.',
            'data' => [
                'cart' => $cart,
                'total' => round($total, 2),
            ],
        ], 200);
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $item = DB::transaction(function () use ($validated, $cart) {
            $product = Product::whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $item = $cart->items()
                ->where('product_id', $product->id)
                ->first();

            $newQuantity = ($item?->quantity ?? 0)
                + $validated['quantity'];

            if ($newQuantity > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => [
                        'The requested quantity exceeds available stock.',
                    ],
                ]);
            }

            if ($item) {
                $item->update([
                    'quantity' => $newQuantity,
                ]);
            } else {
                $item = $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                ]);
            }

            return $item;
        });

        $item->load('product');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'data' => $item,
        ], 201);
    }

    /**
     * Update a cart item's quantity.
     */
    public function update(Request $request, string $itemId)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found.',
            ], 404);
        }

        $item = $cart->items()->with('product')->find($itemId);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        if ($validated['quantity'] > $item->product->stock) {
            throw ValidationException::withMessages([
                'quantity' => [
                    'The requested quantity exceeds available stock.',
                ],
            ]);
        }

        $item->update([
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully.',
            'data' => $item->fresh()->load('product'),
        ], 200);
    }

    /**
     * Remove a product from the cart.
     */
    public function destroy(Request $request, string $itemId)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found.',
            ], 404);
        }

        $item = $cart->items()->find($itemId);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully.',
        ], 200);
    }

    /**
     * Clear the cart.
     */
    public function clear(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully.',
        ], 200);
    }
}