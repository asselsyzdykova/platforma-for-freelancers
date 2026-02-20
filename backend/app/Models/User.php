<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'university',
        'study_year',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['avatar_url', 'university', 'study_year'];

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

    public function getAvatarUrlAttribute()
    {
        return $this->clientProfile?->avatar_url ?? null;
    }

    public function getUniversityAttribute()
    {
        return $this->attributes['university'] ?? null;
    }

    public function getStudyYearAttribute()
    {
        return $this->attributes['study_year'] ?? null;
    }

    /**
     * Get the freelancer profile for the user.
     */
    public function freelancerProfile()
    {
        return $this->hasOne(FreelancerProfile::class);
    }
    public function clientProfile()
    {
    return $this->hasOne(ClientProfile::class);
    }

    public function manager()
    {
    return $this->hasOne(Manager::class);
    }


}
