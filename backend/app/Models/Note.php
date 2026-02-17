<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Note extends Model
{
     use HasFactory;

    protected $fillable = [
        'manager_id',
        'title',
        'body',
        'date',
    ];

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }
}
