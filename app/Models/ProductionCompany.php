<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionCompany extends Model
{
    protected $table = "production_companies";

    protected $fillable = ['name', 'country'];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];


    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
