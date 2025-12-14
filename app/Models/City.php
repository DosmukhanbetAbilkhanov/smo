<?php

namespace App\Models;

use App\Support\Helpers\LocalizedAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\CityFactory> */
    use HasFactory;

    protected $fillable = ['name_ru', 'name_kz'];

    public function getNameAttribute(): string
    {
        return LocalizedAttribute::get($this, 'name') ?? '';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
