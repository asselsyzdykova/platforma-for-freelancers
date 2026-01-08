<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
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

    public function proposals() {
    return $this->hasMany(\App\Models\Proposal::class);
}


    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
