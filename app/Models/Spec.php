<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spec extends Model
{
    /** @use HasFactory<\Database\Factories\SpecFactory> */
    use HasFactory;

    protected $fillable = ['name_ru', 'name_kz'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_specs')
            ->withPivot('is_required')
            ->withTimestamps();
    }
}
