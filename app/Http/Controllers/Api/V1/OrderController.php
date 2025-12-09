<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Order\CreateOrderFromCartAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get all orders for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['shop', 'items.product.nomenclature.unit', 'deliveryCity'])
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success(
            OrderResource::collection($orders),
            'Orders retrieved successfully'
        );
    }

    /**
     * Create a new order from cart.
     */
    public function store(StoreOrderRequest $request, CreateOrderFromCartAction $action): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('shop_id', $request->validated('shop_id'))
            ->firstOrFail();

        try {
            $order = $action->execute(
                $cart,
                $request->validated('delivery_address'),
                $request->validated('delivery_city_id'),
                $request->validated('contact_phone'),
                $request->validated('delivery_entry'),
                $request->validated('delivery_floor'),
                $request->validated('delivery_apartment'),
                $request->validated('delivery_intercom'),
                $request->validated('delivery_notes')
            );

            return ApiResponse::success(
                new OrderResource($order),
                'Order created successfully',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                400
            );
        }
    }

    /**
     * Get a specific order.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['shop', 'items.product.nomenclature.unit', 'deliveryCity'])
            ->findOrFail($id);

        return ApiResponse::success(
            new OrderResource($order),
            'Order retrieved successfully'
        );
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if (! in_array($order->status, ['pending', 'confirmed'])) {
            return ApiResponse::error(
                'Order cannot be cancelled',
                400
            );
        }

        foreach ($order->items as $item) {
            $item->product->increment('quantity', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);

        $order->load(['shop', 'items.product.nomenclature.unit', 'deliveryCity']);

        return ApiResponse::success(
            new OrderResource($order),
            'Order cancelled successfully'
        );
    }
}
