@extends('layouts.app')
@section('title', 'Профиль: ' . $user->name)
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="profile-header">
            <div class="profile-avatar">
                @if ($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar-img">
                @else
                    <i class="fas fa-user-circle default-avatar"></i>
                @endif
            </div>
            <h1>{{ $user->name }}</h1>
            <div class="profile-rating">
                <span class="rating-value">{{ number_format($user->averageRating(), 1) }}/5</span>
                <span class="stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($user->averageRating()) ? 'filled' : '' }}"></i>
                    @endfor
                </span>
                <span class="reviews-count">({{ $user->reviewsReceived->count() }} отзывов)</span>
            </div>
            <p><strong>Телефон:</strong> {{ $user->phone ?? 'Не указан' }}</p>
        </div>

        <div class="profile-reviews">
            <h2><i class="fas fa-star"></i> Отзывы</h2>
            @auth
                @if ($user->id !== auth()->id())
                    @if (!Auth::user()->reviewsGiven()->where('reviewed_id', $user->id)->exists())
                        <form action="{{ route('reviews.store', $user->id) }}" method="POST" class="review-form">
                            @csrf
                            <div class="form-group">
                                <label for="rating">Оценка:</label>
                                <select name="rating" id="rating" required>
                                    <option value="" disabled selected>Выберите оценку</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }} звёзд{{ $i == 1 ? 'а' : 'ы' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="comment">Комментарий:</label>
                                <textarea name="comment" id="comment" rows="4" maxlength="1000" placeholder="Ваш отзыв..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Отправить отзыв
                            </button>
                        </form>
                    @else
                        <p class="review-notice">Вы уже оставили отзыв этому пользователю.</p>
                    @endif
                @endif
            @endauth
            @if ($user->reviewsReceived->isNotEmpty())
                <div class="reviews-list">
                    @foreach ($user->reviewsReceived as $review)
                        <div class="review-item">
                            <div class="review-header">
                                <p class="reviewer-name">{{ $review->reviewer->name }}</p>
                                <span class="stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <p class="review-comment">{{ $review->comment ?? 'Без комментария' }}</p>
                            <p class="review-time">{{ $review->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="no-reviews">Нет отзывов.</p>
            @endif
        </div>

        <div class="profile-ads">
            <h2><i class="fas fa-bullhorn"></i> Активные объявления</h2>
            @if ($activeAds->isNotEmpty())
                <div class="ads-grid">
                    @foreach ($activeAds as $ad)
                        <div class="ad-card">
                            <div class="ad-image">
                                @if ($photo = ($ad->photos->where('is_main', true)->first() ?? $ad->photos->first()))
                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $ad->title }}" loading="lazy">
                                @else
                                    <div class="no-image">Нет фото</div>
                                @endif
                            </div>
                            <div class="ad-info">
                                <h3><a href="{{ route('ad.show', $ad->id) }}">{{ $ad->title }}</a></h3>
                                <p class="price">{{ number_format($ad->price, 0, '.', ' ') }} руб.</p>
                                <p class="category">{{ $ad->category->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="no-ads">Нет активных объявлений.</p>
            @endif

            <h2><i class="fas fa-archive"></i> Неактивные объявления</h2>
            @if ($inactiveAds->isNotEmpty())
                <div class="ads-grid">
                    @foreach ($inactiveAds as $ad)
                        <div class="ad-card">
                            <div class="ad-image">
                                @if ($photo = ($ad->photos->where('is_main', true)->first() ?? $ad->photos->first()))
                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $ad->title }}" loading="lazy">
                                @else
                                    <div class="no-image">Нет фото</div>
                                @endif
                            </div>
                            <div class="ad-info">
                                <h3><a href="{{ route('ad.show', $ad->id) }}">{{ $ad->title }}</a></h3>
                                <p class="price">{{ number_format($ad->price, 0, '.', ' ') }} руб.</p>
                                <p class="category">{{ $ad->category->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="no-ads">Нет неактивных объявлений.</p>
            @endif
        </div>
    </div>
@endsection