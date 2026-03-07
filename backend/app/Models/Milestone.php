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
        'price'
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(FreelancerProject::class, 'project_id');
    }
}
