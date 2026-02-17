<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'title',
        'detail',
        'time',
    ];

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }
}
