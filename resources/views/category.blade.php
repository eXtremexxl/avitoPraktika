@extends('layouts.app')
@section('title', $category->name)
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ads.css') }}">
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
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('category.show', $cat->id) }}"
                               class="{{ $cat->id == $category->id ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <main class="main-content">
                <h1>{{ $category->name }}</h1>
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
                        <p class="no-results">В этой категории нет объявлений.</p>
                    @endforelse
                </div>
            </main>
        </div>
    </div>
@endsection