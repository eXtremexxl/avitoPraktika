<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $userId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $reviewed = User::findOrFail($userId);

        if ($reviewed->id === Auth::id()) {
            return redirect()->back()->with('error', 'Нельзя оставить отзыв себе');
        }

        $existingReview = Review::where('reviewer_id', Auth::id())
            ->where('reviewed_id', $reviewed->id)
            ->exists();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Вы уже оставили отзыв этому пользователю');
        }

        Review::create([
            'reviewer_id' => Auth::id(),
            'reviewed_id' => $reviewed->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Отзыв успешно отправлен');
    }
}