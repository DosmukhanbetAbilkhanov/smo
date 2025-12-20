<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\UnitFactory> */
    use HasFactory;

    protected $fillable = [
        'name_ru',
        'name_kz',
        'abbreviation_ru',
        'abbreviation_kz',
        'shortname_ru',
        'shortname_kz',
    ];

    public function nomenclatures()
    {
        return $this->hasMany(Nomenclature::class);
    }
}
