<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name_ru', 'name_kz', 'nomenclature_id', 'shop_id', 'price', 'quantity', 'images', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
        ];
    }

    public function nomenclature()
    {
        return $this->belongsTo(Nomenclature::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function specs()
    {
        return $this->hasMany(ProductSpec::class);
    }
}
