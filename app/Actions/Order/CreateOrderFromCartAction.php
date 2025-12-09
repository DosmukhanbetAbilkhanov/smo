<?php

namespace App\Actions\Order;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderFromCartAction
{
    public function execute(
        Cart $cart,
        string $deliveryAddress,
        int $deliveryCityId,
        string $contactPhone,
        ?string $deliveryEntry = null,
        ?string $deliveryFloor = null,
        ?string $deliveryApartment = null,
        ?string $deliveryIntercom = null,
        ?string $deliveryNotes = null
    ): Order {
        return DB::transaction(function () use (
            $cart,
            $deliveryAddress,
            $deliveryCityId,
            $contactPhone,
            $deliveryEntry,
            $deliveryFloor,
            $deliveryApartment,
            $deliveryIntercom,
            $deliveryNotes
        ) {
            $cart->load(['items.product', 'shop']);

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Cart is empty',
                ]);
            }

            foreach ($cart->items as $item) {
                if ($item->product->quantity < $item->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for product: {$item->product->name_ru}",
                    ]);
                }
            }

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $cart->user_id,
                'shop_id' => $cart->shop_id,
                'status' => 'pending',
                'subtotal' => $cart->total,
                'total' => $cart->total,
                'delivery_address' => $deliveryAddress,
                'delivery_entry' => $deliveryEntry,
                'delivery_floor' => $deliveryFloor,
                'delivery_apartment' => $deliveryApartment,
                'delivery_intercom' => $deliveryIntercom,
                'delivery_city_id' => $deliveryCityId,
                'contact_phone' => $contactPhone,
                'delivery_notes' => $deliveryNotes,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->quantity * $item->price,
                ]);

                $item->product->decrement('quantity', $item->quantity);
            }

            $cart->items()->delete();

            return $order->load(['items.product.nomenclature.unit', 'shop', 'deliveryCity']);
        });
    }

    private function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = Order::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastOrder ? ((int) substr($lastOrder->order_number, -5)) + 1 : 1;

        return 'ORD-'.$year.'-'.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
