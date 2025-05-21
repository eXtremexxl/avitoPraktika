<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'title', 'category_id', 'price', 'description', 'contact', 'is_active', 'approved', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}