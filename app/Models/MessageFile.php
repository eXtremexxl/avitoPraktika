<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageFile extends Model
{
    protected $fillable = ['message_id', 'path', 'original_name', 'mime_type', 'size'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}