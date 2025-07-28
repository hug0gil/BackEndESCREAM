<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Director extends Model
{

    protected $fillable = ['name', 'birth_date'];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];


    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
