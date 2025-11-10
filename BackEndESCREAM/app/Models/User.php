<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use function Termwind\renderUsing;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plan_id',
        'admin_level'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'created_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Verifica si la suscripción está activa (accessor simple)
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscribed
            && $this->end_date
            && now()->lessThanOrEqualTo($this->end_date);
    }

    /**
     * Días restantes (accessor simple)
     */
    public function daysRemaining(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        $days = now()->diffInDays($this->end_date, false);
        return $days > 0 ? (int)$days : 0;
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class); // user -> pertenece a un plan
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
