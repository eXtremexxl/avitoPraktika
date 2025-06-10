<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['ad_id', 'seller_id', 'buyer_id', 'deleted_by_user'];

    protected $casts = [
        'deleted_by_user' => 'array',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function unreadMessagesCount()
    {
        return $this->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    /**
     * Проверяет, удалён ли чат для текущего пользователя.
     */
    public function isDeletedForCurrentUser()
    {
        return !empty($this->deleted_by_user) && in_array((string) auth()->id(), array_map('strval', $this->deleted_by_user));
    }
}