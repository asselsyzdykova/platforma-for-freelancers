<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'freelancer_id',
        'message',
        'budget',
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function freelancer() {
        return $this->belongsTo(\App\Models\User::class, 'freelancer_id');
    }

}
