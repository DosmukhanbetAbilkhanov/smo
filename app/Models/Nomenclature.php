<?php

namespace App\Models;

use App\Enums\NomenclatureStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomenclature extends Model
{
    /** @use HasFactory<\Database\Factories\NomenclatureFactory> */
    use HasFactory;

    protected $fillable = [
        'name_ru', 'name_kz', 'unit_id', 'category_id',
        'description_ru', 'description_kz', 'SKU', 'GTIN', 'NTIN', 'brandname',
        'status', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => NomenclatureStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
