<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;


class Internship extends Model
{
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable= [
        'client_id',
        'title',
        'company',
        'description',
        'stipendType',
        'price',
        'time',
        'number'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
