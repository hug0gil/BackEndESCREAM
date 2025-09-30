<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ["user_id", "profile_name", "devices_allowed"];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];



    public function user()
    {
        return $this->belongsTo(User::class); // El que tiene la foranea es el que pertenece a X
    }
}
