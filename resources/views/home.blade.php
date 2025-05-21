@extends('layouts.app')
@section('title', 'Главная')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="home-layout">
            <aside class="sidebar">
                <h2>Категории</h2>
                <ul>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('category.show', $category->id) }}"
                               class="{{ request()->route('id') == $category->id ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <main class="main-content">
                <form method="GET" action="{{ route('home') }}" class="search-form">
                    <div class="search-wrapper">
                        <input type="text" name="search" placeholder="Поиск по объявлениям..." value="{{ request('search') }}">
                        <button type="submit" aria-label="Найти">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 19L13.5 13.5M15 8.5C15 12.0899 12.0899 15 8.5 15C4.91015 15 2 12.0899 2 8.5C2 4.91015 4.91015 2 8.5 2C12.0899 2 15 4.91015 15 8.5Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="ads-grid">
                    @forelse ($ads as $ad)
                        <a href="{{ route('ad.show', $ad->id) }}" class="ad-card">
                            <div class="ad-image">
                                @php
                                    $mainPhoto = $ad->photos->where('is_main', true)->first() ?? $ad->photos->first();
                                @endphp
                                @if ($mainPhoto)
                                    <img src="{{ asset('storage/' . $mainPhoto->path) }}" alt="{{ $ad->title }}" loading="lazy">
                                @else
                                    <div class="no-image">Нет фото</div>
                                @endif
                            </div>
                            <div class="ad-info">
                                <h3>{{ $ad->title }}</h3>
                                <p class="price">{{ number_format($ad->price, 0, '.', ' ') }} руб.</p>
                                <p class="category">{{ $ad->category->name }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="no-results">Объявлений не найдено.</p>
                    @endforelse
                </div>
            </main>
        </div>
    </div>
@endsection