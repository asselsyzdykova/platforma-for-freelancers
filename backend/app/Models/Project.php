<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use \App\Models\Proposal;

class Project extends Model
{
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'budget',
        'category',
        'tags',
        'status',
    ];


    protected $casts = [
        'tags' => 'array',
    ];

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }


    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
}
