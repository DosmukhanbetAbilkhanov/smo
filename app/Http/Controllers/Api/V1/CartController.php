<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CartResource;
use App\Http\Responses\ApiResponse;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Get all carts for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $carts = Cart::where('user_id', $request->user()->id)
            ->with(['shop', 'items.product.nomenclature.unit', 'items.product.shop'])
            ->get();

        return ApiResponse::success(
            CartResource::collection($carts),
            'Carts retrieved successfully'
        );
    }

    /**
     * Get cart for a specific shop.
     */
    public function show(Request $request, int $shopId): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user(), $shopId);

        $cart->load(['shop', 'items.product.nomenclature.unit', 'items.product.shop']);

        return ApiResponse::success(
            new CartResource($cart),
            'Cart retrieved successfully'
        );
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if product is in stock
        if ($product->quantity < ($validated['quantity'] ?? 1)) {
            return ApiResponse::error(
                'Insufficient stock available',
                400
            );
        }

        // Get or create cart for the product's shop
        $cart = $this->getOrCreateCart($request->user(), $product->shop_id);

        try {
            DB::beginTransaction();

            // Check if item already exists in cart
            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                // Update quantity
                $newQuantity = $cartItem->quantity + ($validated['quantity'] ?? 1);

                if ($product->quantity < $newQuantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Insufficient stock available',
                    ]);
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                // Create new cart item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'] ?? 1,
                    'price' => $product->price,
                ]);
            }

            DB::commit();

            $cart->load(['shop', 'items.product.nomenclature.unit', 'items.product.shop']);

            return ApiResponse::success(
                new CartResource($cart),
                'Item added to cart successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                $e->getMessage(),
                400
            );
        }
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, int $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Find cart item that belongs to user
        $cartItem = CartItem::whereHas('cart', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($itemId);

        // Check if product has enough stock
        if ($cartItem->product->quantity < $validated['quantity']) {
            return ApiResponse::error(
                'Insufficient stock available',
                400
            );
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        $cart = $cartItem->cart;
        $cart->load(['shop', 'items.product.nomenclature.unit', 'items.product.shop']);

        return ApiResponse::success(
            new CartResource($cart),
            'Cart updated successfully'
        );
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, int $itemId): JsonResponse
    {
        // Find cart item that belongs to user
        $cartItem = CartItem::whereHas('cart', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($itemId);

        $cart = $cartItem->cart;
        $cartItem->delete();

        $cart->load(['shop', 'items.product.nomenclature.unit', 'items.product.shop']);

        return ApiResponse::success(
            new CartResource($cart),
            'Item removed from cart successfully'
        );
    }

    /**
     * Clear all items from a specific cart.
     */
    public function clear(Request $request, int $shopId): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('shop_id', $shopId)
            ->firstOrFail();

        $cart->items()->delete();

        $cart->load(['shop', 'items.product.nomenclature.unit', 'items.product.shop']);

        return ApiResponse::success(
            new CartResource($cart),
            'Cart cleared successfully'
        );
    }

    /**
     * Get or create a cart for the user and shop.
     */
    private function getOrCreateCart($user, int $shopId): Cart
    {
        return Cart::firstOrCreate(
            [
                'user_id' => $user->id,
                'shop_id' => $shopId,
            ]
        );
    }
}
