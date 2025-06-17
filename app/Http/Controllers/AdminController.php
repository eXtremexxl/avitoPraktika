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
        $userCount = User::count();
        $adCount = Ad::count();
        $categoryCount = Category::count();
        return view('admin.dashboard', compact('userCount', 'adCount', 'categoryCount'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(10)->appends(['search' => $search]);
        return view('admin.users', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users_edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::findOrFail($id);
        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'Пользователь обновлён');
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();

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

        return redirect()->route('admin.categories')->with('success', 'Категория создана');
    }

    public function destroyCategory($id)
    {
        Category::findOrFail($id)->delete();

        return redirect()->route('admin.categories')->with('success', 'Категория удалена');
    }

    public function ads(Request $request)
    {
        $query = Ad::with('photos');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('is_active', $status == 'active' ? 1 : 0);
        }

        $ads = $query->paginate(10)->appends(['search' => $search, 'status' => $status]);
        return view('admin.ads', compact('ads'));
    }

    public function approveAd($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->update(['approved' => true]);

        return redirect()->route('admin.ads')->with('success', 'Объявление подтверждено');
    }

    public function destroyAd($id)
    {
        $ad = Ad::findOrFail($id);
        foreach ($ad->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $ad->delete();

        return redirect()->route('admin.ads')->with('success', 'Объявление удалено');
    }
}