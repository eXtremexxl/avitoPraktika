@extends('layouts.app')
@section('title', 'Личный кабинет')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.min.js"></script>
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
                @if (auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="avatar-img">
                @else
                    <i class="far fa-user-circle default-avatar"></i>
                @endif
            </div>
            <h1>{{ auth()->user()->name }}</h1>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Телефон:</strong> {{ auth()->user()->phone ?? 'Не указан' }}</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Редактировать профиль
            </a>
        </div>

        <div class="tabs">
            <button class="tab-button active" data-tab="active"><i class="fas fa-bullhorn"></i> Активные</button>
            <button class="tab-button" data-tab="inactive"><i class="fas fa-archive"></i> Неактивные</button>
            <button class="tab-button" data-tab="pending"><i class="fas fa-clock"></i> На модерации</button>
            <button class="tab-button" data-tab="chats"><i class="fas fa-comments"></i> Чаты</button>
            <button class="tab-button" data-tab="reviews"><i class="fas fa-star"></i> Отзывы</button>
        </div>

        <div id="active" class="tab-content active">
            <h2><i class="fas fa-bullhorn"></i> Активные объявления</h2>
            @if (empty($ads['active']) || collect($ads['active'])->isEmpty())
                <p class="no-results"><i class="fas fa-exclamation-circle"></i> Нет активных объявлений.</p>
            @else
                <div class="ads-grid">
                    @foreach ($ads['active'] as $ad)
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
                                <div class="ad-actions">
                                    <a href="{{ route('ad.edit', $ad->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Редактировать
                                    </a>
                                    <form 
                                        action="{{ route('ad.destroy', $ad->id) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Удалить объявление?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="inactive" class="tab-content">
            <h2><i class="fas fa-archive"></i> Неактивные объявления</h2>
            @if (empty($ads['inactive']) || collect($ads['inactive'])->isEmpty())
                <p class="no-results"><i class="fas fa-exclamation-circle"></i> Нет неактивных объявлений.</p>
            @else
                <div class="ads-grid">
                    @foreach ($ads['inactive'] as $ad)
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
                                <div class="ad-actions">
                                    <a href="{{ route('ad.edit', $ad->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Редактировать
                                    </a>
                                    <form 
                                        action="{{ route('ad.destroy', $ad->id) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Удалить объявление?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="pending" class="tab-content">
            <h2><i class="fas fa-clock"></i> Объявления на модерации</h2>
            @if (empty($ads['pending']) || collect($ads['pending'])->isEmpty())
                <p class="no-results"><i class="fas fa-exclamation-circle"></i> Нет объявлений на модерации.</p>
            @else
                <div class="ads-grid">
                    @foreach ($ads['pending'] as $ad)
                        <div class="ad-card">
                            <div class="ad-image">
                                @if ($photo = ($ad->photos->where('is_main', true)->first() ?? $ad->photos->first()))
                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $ad->title }}" loading="lazy">
                                @else
                                    <div class="no-image">Нет фото</div>
                                @endif
                            </div>
                            <div class="ad-info">
                                <h3>{{ $ad->title }}</h3>
                                <p class="price">{{ number_format($ad->price, 0, '.', ' ') }} руб.</p>
                                <p class="category">{{ $ad->category->name }}</p>
                                <div class="ad-actions">
                                    <a href="{{ route('ad.edit', $ad->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Редактировать
                                    </a>
                                    <form 
                                        action="{{ route('ad.destroy', $ad->id) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Удалить объявление?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="chats" class="tab-content">
            <h2><i class="fas fa-comments"></i> Мои чаты</h2>
            @if ($chats->isEmpty())
                <p class="no-results"><i class="fas fa-exclamation-circle"></i> Нет активных чатов.</p>
            @else
                <div class="ads-grid">
                    @foreach ($chats as $chat)
                        <div class="chat-card">
                            <div class="chat-image">
                                @if ($photo = ($chat->ad->photos->where('is_main', true)->first() ?? $chat->ad->photos->first()))
                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $chat->ad->title }}" loading="lazy">
                                @else
                                    <div class="no-image">Нет фото</div>
                                @endif
                            </div>
                            <div class="chat-info">
                                <h3>
                                    <a href="{{ route('chat.show', $chat->id) }}">
                                        {{ $chat->ad->title }}
                                    </a>
                                </h3>
                                <p class="chat-partner">
                                    <strong>Собеседник:</strong>
                                    {{ $chat->seller_id === auth()->id() ? $chat->buyer->name : $chat->seller->name }}
                                </p>
                                <p class="chat-last-message">
                                    <strong>Последнее сообщение:</strong>
                                    {{ $chat->messages->last()->content ?? 'Нет сообщений' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="reviews" class="tab-content">
            <h2><i class="fas fa-star"></i> Мои отзывы ({{ number_format(auth()->user()->averageRating(), 1) }}/5)</h2>
            @if (auth()->user()->reviewsReceived->isNotEmpty())
                <div class="reviews-list">
                    @foreach (auth()->user()->reviewsReceived as $review)
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
                <p class="no-results"><i class="fas fa-exclamation-circle"></i> Нет отзывов.</p>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-button:not(.tab-favorites)');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    tab.classList.add('active');
                    document.getElementById(tab.dataset.tab).classList.add('active');
                });
            });


        });


    </script>
@endsection