<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'year', 'synopsis', 'image', 'rating', 'director_id', 'production_company_id', 'country'];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'movie_actor', 'movie_id', 'actor_id');
    }

    public function subgenres()
    {
        return $this->belongsToMany(Subgenre::class, 'movie_subgenre', 'movie_id', 'subgenre_id');
    }

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function productionCompany()
    {
        return $this->belongsTo(ProductionCompany::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
