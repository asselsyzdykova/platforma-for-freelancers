<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FreelancerProject;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelancer_project_id',
        'title',
        'price',
        'status',
        'payment_status',
        'paid_at',
        'stripe_session_id'
    ];

    protected $casts = [
        'price' => 'float',
        'paid_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(FreelancerProject::class, 'freelancer_project_id');
    }
}
