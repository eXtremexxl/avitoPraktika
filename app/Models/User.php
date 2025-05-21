<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'avatar',
    ];

    protected $hidden = [
        'password',
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'seller_id')->orWhere('buyer_id', $this->id);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewed_id');
    }

    public function averageRating()
    {
        return $this->reviewsReceived()->avg('rating') ?: 0;
    }
}