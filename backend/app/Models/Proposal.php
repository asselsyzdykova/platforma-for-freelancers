<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'freelancer_id',
        'message',
        'budget',
        'status',
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function freelancer() {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
