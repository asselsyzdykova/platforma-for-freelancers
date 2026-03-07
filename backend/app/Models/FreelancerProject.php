<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Milestone;

class FreelancerProject extends Model
{
    use HasFactory;

    protected $table = 'freelancer_projects';

    protected $fillable = [
        'freelancer_id',
        'client_id',
        'name',
        'description',
        'deadline'
    ];

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
