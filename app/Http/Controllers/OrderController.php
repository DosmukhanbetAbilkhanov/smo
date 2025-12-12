<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the user's orders.
     */
    public function index(Request $request): Response
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['shop', 'items.product.nomenclature.unit', 'deliveryCity'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['shop', 'items.product.nomenclature.unit', 'deliveryCity'])
            ->find($id);

        if (! $order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found');
        }

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Display the order confirmation page.
     */
    public function confirmation(Request $request, int $id): Response|RedirectResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['shop', 'items.product.nomenclature.unit', 'deliveryCity'])
            ->find($id);

        if (! $order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found');
        }

        return Inertia::render('Orders/Confirmation', [
            'order' => $order,
        ]);
    }
}
