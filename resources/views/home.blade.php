@extends('layouts.app')

@section('title', 'Главная')

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
                    <li>
                        <a href="{{ route('home') }}"
                           class="{{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="fas fa-th-large"></i>
                            <span>Все</span>
                        </a>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('category.show', $category->id) }}"
                               class="{{ request()->routeIs('category.show') && request()->route('category') == $category->id ? 'active' : '' }}">
                                <i class="fas fa-tag"></i>
                                <span>{{ $category->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <main class="main-content">
                <button class="toggle-categories" aria-label="Показать/скрыть категории">
                    <i class="fas fa-bars"></i> Категории
                </button>

                <form method="GET" action="{{ route('home') }}" class="search-form">
                    <div class="search-wrapper">
                        <input type="text" name="search" placeholder="Поиск по объявлениям..." value="{{ request('search') }}">
                        <button type="submit" aria-label="Найти">
                            <i class="fas fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

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
                        <p class="no-results">Объявлений не найдено.</p>
                    @endforelse
                </div>
            </main>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.querySelector('.toggle-categories');
        const sidebar = document.querySelector('.sidebar');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('active');
                this.classList.toggle('active');
                document.body.classList.toggle('menu-open'); 
            });
        }
    });
</script>


@endsection