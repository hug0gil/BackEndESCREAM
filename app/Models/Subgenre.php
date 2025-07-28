<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subgenre extends Model
{

    protected $fillable = ['name'];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];


    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_subgenre', 'subgenre_id', 'movie_id');
    }
}
