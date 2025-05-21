<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Request $request, $adId)
    {
        $ad = Ad::findOrFail($adId);
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)->where('ad_id', $ad->id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['success' => true, 'is_favorited' => false, 'message' => 'Удалено из избранного']);
        }

        Favorite::create([
            'user_id' => $user->id,
            'ad_id' => $ad->id,
        ]);

        return response()->json(['success' => true, 'is_favorited' => true, 'message' => 'Добавлено в избранное']);
    }

    public function index()
    {
        $favorites = Auth::user()->favorites()->with(['ad.photos', 'ad.category'])->get()->pluck('ad');
        return view('favorites.index', compact('favorites'));
    }
}