<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'shop_id',
        'status',
        'subtotal',
        'total',
        'delivery_address',
        'delivery_entry',
        'delivery_floor',
        'delivery_apartment',
        'delivery_intercom',
        'delivery_city_id',
        'contact_phone',
        'delivery_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function deliveryCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'delivery_city_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
