<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        // ✅ Редиректим обратно к списку пользователей
        return redirect()->route('admin.users')->with('success', 'Пользователь обновлён');
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();

        // ✅ Редиректим обратно к списку пользователей
        return redirect()->route('admin.users')->with('success', 'Пользователь удалён');
    }

    public function categories()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        Category::create($request->all());

        // ✅ Редиректим обратно к списку категорий
        return redirect()->route('admin.categories')->with('success', 'Категория создана');
    }

    public function destroyCategory($id)
    {
        Category::findOrFail($id)->delete();

        // ✅ Редиректим обратно к списку категорий
        return redirect()->route('admin.categories')->with('success', 'Категория удалена');
    }

    public function ads()
    {
        $ads = Ad::with('photos')->get();
        return view('admin.ads', compact('ads'));
    }

    public function approveAd($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->update(['approved' => true]);

        // ✅ Редиректим обратно к списку объявлений
        return redirect()->route('admin.ads')->with('success', 'Объявление подтверждено');
    }

    public function destroyAd($id)
    {
        $ad = Ad::findOrFail($id);
        foreach ($ad->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $ad->delete();

        // ✅ Редиректим обратно к списку объявлений
        return redirect()->route('admin.ads')->with('success', 'Объявление удалено');
    }
}
