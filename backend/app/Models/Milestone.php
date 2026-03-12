<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FreelancerProject;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'price',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'price' => 'float',
        'paid_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(FreelancerProject::class, 'project_id');
    }
}
