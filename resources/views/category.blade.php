@extends('layouts.app')

@section('title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
                                <i class="fas fa-tag"></i>
                                <span>{{ $cat->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <main class="main-content">
                <h1>{{ $category->name }}</h1>
                <div class="ads-grid">
                    @forelse ($ads as $index => $ad)
                        <a href="{{ route('ad.show', $ad->id) }}" class="ad-card" style="--card-index: {{ $index }}">
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