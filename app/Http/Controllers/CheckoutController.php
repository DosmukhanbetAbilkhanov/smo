<?php

namespace App\Http\Controllers;

use App\Actions\Order\CreateOrderFromCartAction;
use App\Models\Cart;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        // Get all user's carts with items
        $carts = Cart::where('user_id', $user->id)
            ->with(['shop', 'items.product'])
            ->get();

        // If no carts or all carts are empty, redirect to cart page
        if ($carts->isEmpty() || $carts->every(fn ($cart) => $cart->items->isEmpty())) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Get all cities for delivery selection
        $cities = City::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Cart/Checkout', [
            'carts' => $carts,
            'cities' => $cities,
            'defaultPhone' => $user->phone,
        ]);
    }

    /**
     * Process the checkout and create orders.
     */
    public function store(Request $request, CreateOrderFromCartAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'delivery_address' => 'required|string|max:500',
            'delivery_entry' => 'nullable|string|max:50',
            'delivery_floor' => 'nullable|string|max:50',
            'delivery_apartment' => 'nullable|string|max:50',
            'delivery_intercom' => 'nullable|string|max:50',
            'delivery_city_id' => 'required|exists:cities,id',
            'contact_phone' => 'required|string|max:20',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('shop_id', $validated['shop_id'])
            ->firstOrFail();

        try {
            $order = $action->execute(
                $cart,
                $validated['delivery_address'],
                $validated['delivery_city_id'],
                $validated['contact_phone'],
                $validated['delivery_entry'] ?? null,
                $validated['delivery_floor'] ?? null,
                $validated['delivery_apartment'] ?? null,
                $validated['delivery_intercom'] ?? null,
                $validated['delivery_notes'] ?? null
            );

            return redirect()->route('orders.confirmation', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
