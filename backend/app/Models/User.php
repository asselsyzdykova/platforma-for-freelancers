<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['avatar_url', 'university', 'study_year', 'subscription_status', 'plan'];

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
        $profile = $this->freelancerProfile ?: $this->clientProfile;

        if (!$profile || !$profile->avatar) {
            return null;
        }

        return Storage::url($profile->avatar);
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

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }
    public function hasActiveAccess(): bool
    {
        $sub = $this->subscription;
        if (!$sub) return false;

        if ($sub->status === 'active') return true;

        if ($sub->status === 'canceled' && $sub->end_date && $sub->end_date->isFuture()) {
            return true;
        }

        return false;
    }
    public function getSubscriptionStatusAttribute()
    {
        return $this->subscription?->status ?? 'none';
    }

    public function getPlanAttribute()
    {
        return $this->subscription?->plan ?? 'free';
    }
}
