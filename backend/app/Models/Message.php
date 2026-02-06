<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'read_at',
    ];

    protected $appends = ['attachment_url'];

    public function getAttachmentUrlAttribute()
    {
        if (! $this->attachment_path) {
            return null;
        }

        return asset('storage/chat-attachments/' . $this->attachment_path);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
