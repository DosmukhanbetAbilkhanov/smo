<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySpec extends Model
{
    /** @use HasFactory<\Database\Factories\CategorySpecFactory> */
    use HasFactory;

    protected $fillable = ['category_id', 'spec_id', 'is_required'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function spec()
    {
        return $this->belongsTo(Spec::class);
    }
}
