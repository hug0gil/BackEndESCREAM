<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ["name", "price", "devices_allowed",];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];


    public function users()
    {
        return $this->hasMany(User::class); // plan -> tiene muchos usuarios
    }
}
