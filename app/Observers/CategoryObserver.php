<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Category "creating" event.
     */
    public function creating(Category $category): void
    {
        if (empty($category->slug) && ! empty($category->name_ru)) {
            $category->slug = Str::slug($category->name_ru);
        }
    }

    /**
     * Handle the Category "updating" event.
     */
    public function updating(Category $category): void
    {
        if ($category->isDirty('name_ru')) {
            $category->slug = Str::slug($category->name_ru);
        }
    }
}
