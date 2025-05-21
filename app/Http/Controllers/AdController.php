<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Photo;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Ad::with('photos')->where('is_active', true)->where('approved', true);
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $ads = $query->get();
        return view('home', compact('categories', 'ads'));
    }

    public function show($id)
    {
        $ad = Ad::with(['photos', 'category', 'user.reviewsReceived.reviewer'])->findOrFail($id);
        if (!$ad->is_active || !$ad->approved) {
            abort(404);
        }

        // Логируем просмотр только если пользователь не просматривал это объявление
        if (Auth::check() && !$ad->views()->where('user_id', Auth::id())->exists()) {
            View::create([
                'ad_id' => $ad->id,
                'user_id' => Auth::id(),
                'viewed_at' => now(),
            ]);
        }

        // Связанные объявления
        $relatedAds = Ad::with(['photos', 'category'])
            ->where('category_id', $ad->category_id)
            ->where('id', '!=', $ad->id)
            ->where('is_active', true)
            ->where('approved', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $categories = Category::all();
        return view('ads.show', compact('ad', 'categories', 'relatedAds'));
    }

    public function showCategory($id)
    {
        $categories = Category::all();
        $category = Category::findOrFail($id);
        $ads = Ad::with('photos')->where('category_id', $id)->where('is_active', true)->where('approved', true)->get();
        return view('category', compact('categories', 'category', 'ads'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('ads.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'contact' => 'required|string|max:255',
            'photos' => 'max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_photo' => 'sometimes|integer|min:0|max:4',
        ]);

        $ad = Auth::user()->ads()->create([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'contact' => $request->input('contact'),
            'is_active' => $request->has('is_active'),
            'approved' => false,
        ]);

        if ($request->hasFile('photos')) {
            $mainPhotoIndex = $request->input('main_photo', 0);
            $photos = $request->file('photos');
            foreach ($photos as $index => $photo) {
                $path = $photo->store('photos', 'public');
                $ad->photos()->create([
                    'path' => $path,
                    'is_main' => $index == $mainPhotoIndex,
                ]);
            }
        }

        return redirect()->route('home')->with('success', 'Объявление создано и ожидает подтверждения');
    }

    public function edit($id)
    {
        $ad = Ad::with('photos')->findOrFail($id);
        if ($ad->user_id !== Auth::id()) {
            abort(403);
        }
        $categories = Category::all();
        return view('ads.edit', compact('ad', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $ad = Ad::with('photos')->findOrFail($id);
        if ($ad->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'contact' => 'required|string|max:255',
            'photos' => 'sometimes|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_photo' => 'sometimes|integer|min:0',
            'delete_photos' => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'category_id', 'price', 'description', 'contact'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $ad->update($data);

        if ($request->filled('delete_photos')) {
            $deleteIds = array_filter(explode(',', $request->delete_photos), 'is_numeric');
            foreach ($deleteIds as $photoId) {
                $photo = $ad->photos()->find($photoId);
                if ($photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }
        }

        $mainPhotoIndex = $request->input('main_photo', 0);
        if ($request->hasFile('photos')) {
            $existingCount = $ad->photos()->count();
            $newPhotos = $request->file('photos');
            if ($existingCount + count($newPhotos) > 5) {
                return back()->withErrors(['photos' => 'Максимум 5 фотографий']);
            }

            foreach ($newPhotos as $index => $photo) {
                $path = $photo->store('photos', 'public');
                $isMain = ($index + $existingCount) == $mainPhotoIndex;
                $ad->photos()->create([
                    'path' => $path,
                    'is_main' => $isMain,
                ]);
            }
        }

        if ($request->has('main_photo') && $ad->photos()->count() > 0) {
            $ad->photos()->update(['is_main' => false]);
            $mainPhoto = $ad->photos()->skip($mainPhotoIndex)->first();
            if ($mainPhoto) {
                $mainPhoto->update(['is_main' => true]);
            } else {
                $firstPhoto = $ad->photos()->first();
                if ($firstPhoto) {
                    $firstPhoto->update(['is_main' => true]);
                }
            }
        }

        if ($data['is_active']) {
            return redirect()->route('ad.show', $ad->id)->with('success', 'Объявление обновлено');
        } else {
            return redirect()->route('home')->with('success', 'Объявление обновлено и деактивировано');
        }
    }

    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);
        if ($ad->user_id !== Auth::id()) {
            abort(403);
        }
        foreach ($ad->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $ad->delete();
        return redirect()->route('home')->with('success', 'Объявление удалено');
    }
}