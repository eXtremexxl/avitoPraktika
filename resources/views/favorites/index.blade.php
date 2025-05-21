<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Избранное - {{ config('app.name', 'Avito') }}</title>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
    @endpush
    <script src="https://kit.fontawesome.com/4a2b98c9d7.js" crossorigin="anonymous"></script>
</head>
<body>
    @extends('layouts.app')

    @section('title', 'Избранное')

    @section('content')
        <div class="container">
            <div class="favorites-header">
                <h1><i class="far fa-heart"></i> Избранное</h1>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($favorites->isEmpty())
                <div class="no-results">
                    <i class="far fa-heart-broken"></i>
                    <p>У вас нет избранных объявлений</p>
                </div>
            @else
                <div class="ads-grid">
                    @foreach ($favorites as $ad)
                        <div class="ad-card" data-ad-id="{{ $ad->id }}">
                            <div class="ad-image">
                                @if ($ad->photos->isNotEmpty())
                                    <img src="{{ asset('storage/' . $ad->photos->first()->path) }}" alt="{{ $ad->title }}">
                                @else
                                    <div class="no-image">Нет изображения</div>
                                @endif
                            </div>
                            <div class="ad-info">
                                <h3><a href="{{ route('ad.show', $ad->id) }}">{{ $ad->title }}</a></h3>
                                <p class="price">{{ number_format($ad->price, 0, '', ' ') }} ₽</p>
                                <p class="category">{{ $ad->category->name }}</p>
                                <div class="ad-actions">
                                    <a href="{{ route('ad.show', $ad->id) }}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> Просмотреть
                                    </a>
                                    <button class="btn btn-danger toggle-favorite" data-ad-id="{{ $ad->id }}">
                                        <i class="fas fa-trash"></i> Удалить
                                    </button>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.toggle-favorite').forEach(button => {
                    button.addEventListener('click', function () {
                        const adId = this.getAttribute('data-ad-id');
                        fetch(`/favorites/${adId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ _method: 'POST' })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (!data.is_favorited) {
                                    const card = document.querySelector(`.ad-card[data-ad-id="${adId}"]`);
                                    card.remove();
                                    if (!document.querySelector('.ad-card')) {
                                        document.querySelector('.ads-grid').outerHTML = `
                                            <div class="no-results">
                                                <i class="far fa-heart-broken"></i>
                                                <p>У вас нет избранных объявлений</p>
                                            </div>`;
                                    }
                                }
                                alert(data.message);
                            } else {
                                alert('Ошибка при изменении избранного');
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                            alert('Произошла ошибка');
                        });
                    });
                });
            });
        </script>
    @endsection
</body>
</html>