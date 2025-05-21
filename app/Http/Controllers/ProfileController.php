<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['index', 'edit', 'update']);
    }

    public function index()
    {
        $user = Auth::user();
        $ads = Ad::with(['photos', 'category'])
            ->where('user_id', $user->id)
            ->get()
            ->groupBy(function ($ad) {
                if (!$ad->approved) {
                    return 'pending';
                }
                return $ad->is_active ? 'active' : 'inactive';
            });

        $chats = $user->chats()->with(['ad', 'seller', 'buyer', 'messages'])->get();

        return view('profile.index', compact('user', 'ads', 'chats'));
    }

    public function show($id)
    {
        $user = User::with(['reviewsReceived.reviewer', 'ads.photos', 'ads.category'])->findOrFail($id);
        $activeAds = $user->ads()->where('is_active', true)->where('approved', true)->get();
        $inactiveAds = $user->ads()->where(function ($query) {
            $query->where('is_active', false)->orWhere('approved', false);
        })->get();

        return view('profile.show', compact('user', 'activeAds', 'inactiveAds'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Обработка аватарки
        if ($request->hasFile('avatar')) {
            // Удаляем старую аватарку, если она есть
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.index')->with('success', 'Профиль обновлён');
    }
}