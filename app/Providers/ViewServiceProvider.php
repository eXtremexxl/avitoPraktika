<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $unreadCount = Auth::check()
                ? Cache::remember('unread_count_' . Auth::id(), now()->addMinutes(5), function () {
                    return Auth::user()->chats()
                        ->withCount(['messages as unread_messages_count' => function ($query) {
                            $query->where('sender_id', '!=', auth()->id())
                                  ->where('is_read', false);
                        }])
                        ->get()
                        ->sum('unread_messages_count');
                })
                : 0;

            $view->with('unreadCount', $unreadCount);
        });
    }
}