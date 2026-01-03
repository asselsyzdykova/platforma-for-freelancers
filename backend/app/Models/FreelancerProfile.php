<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FreelancerProfile extends Model
{
    protected $fillable = [
        'user_id', 'about', 'location', 'skills', 'completed_projects', 'proposals', 'rating', 'reviews'
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
