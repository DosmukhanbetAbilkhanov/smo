<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    /** @use HasFactory<\Database\Factories\UserTypeFactory> */
    use HasFactory;

    protected $fillable = ['name_ru', 'name_kz'];

    public function users()
    {
        return $this->hasMany(User::class, 'type_id');
    }
}
