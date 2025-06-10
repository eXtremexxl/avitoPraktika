@extends('layouts.app')

@section('title', 'Избранное')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')
    <div class="container">
        <div class="favorites-header">
            <h1>Избранное</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if ($favorites->isEmpty())
            <div class="no-results">
                <i class="fas fa-heart-crack"></i>
                <p>У вас нет избранных объявлений</p>
            </div>
        @else
            <div class="ads-grid">
                @foreach ($favorites as $ad)
                    <div class="ad-card" data-ad-id="{{ $ad->id }}">
                        <div class="ad-image">
                            @if ($ad->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $ad->photos->first()->path) }}" alt="{{ $ad->title }}" loading="lazy">
                            @else
                                <div class="no-image">Нет изображения</div>
                            @endif
                        </div>
                        <div class="ad-info">
                            <h3><a href="{{ route('ad.show', $ad->id) }}">{{ $ad->title }}</a></h3>
                            <p class="price">{{ number_format($ad->price, 0, '', ' ') }} ₽</p>
                            <p class="category">{{ $ad->category->name }}</p>
                            <div class="ad-actions">
                                <a href="{{ route('ad.show', $ad->id) }}" class="btn btn-primary full-width">
                                    <i class="fas fa-eye"></i> Просмотреть
                                </a>
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
                    fetch('{{ route('favorites.toggle', 0) }}'.replace('0', adId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && !data.is_favorited) {
                            const card = document.querySelector(`.ad-card[data-ad-id="${adId}"]`);
                            card.remove();
                            if (!document.querySelector('.ad-card')) {
                                document.querySelector('.ads-grid').outerHTML = `
                                    <div class="no-results">
                                        <i class="fas fa-heart-crack"></i>
                                        <p>У вас нет избранных объявлений</p>
                                    </div>`;
                            }
                            alert(data.message || 'Объявление удалено из избранного');
                        } else {
                            alert(data.message || 'Ошибка при удалении из избранного');
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