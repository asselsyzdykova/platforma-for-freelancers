<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'title',
        'description',
        'status',
        'deadline',
    ];

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }
}
