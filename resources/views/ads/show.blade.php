@extends('layouts.app')
@section('title', $ad->title)
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ad-show.css') }}">
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

        <div class="ad-details">
            <h1>{{ $ad->title }}</h1>
            <div class="ad-content">
                <div class="ad-photos">
                    @if ($ad->photos->isNotEmpty())
                        <div class="main-photo">
                            <img 
                                src="{{ asset('storage/' . ($ad->photos->where('is_main', true)->first() ?? $ad->photos->first())->path) }}" 
                                alt="{{ $ad->title }}" 
                                id="main-image"
                                loading="lazy"
                            >
                        </div>
                        @if ($ad->photos->count() > 1)
                            <div class="thumbnails">
                                @foreach ($ad->photos as $index => $photo)
                                    <img 
                                        src="{{ asset('storage/' . $photo->path) }}" 
                                        alt="{{ $ad->title }}" 
                                        class="{{ $photo->is_main ? 'active' : '' }}" 
                                        onclick="changeMainImage('{{ asset('storage/' . $photo->path) }}', this)"
                                        loading="lazy"
                                    >
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="no-photos">Нет фотографий</div>
                    @endif
                </div>
                <div class="ad-info">
                    <p class="price">{{ number_format($ad->price, 0, '.', ' ') }} руб.</p>
                    <p><strong>Категория:</strong> {{ $ad->category->name }}</p>
                    <p><strong>Описание:</strong> {{ $ad->description }}</p>
                    <p><strong>Продавец:</strong> 
                        @if ($ad->user)
                            <a href="{{ route('profile.show', $ad->user->id) }}">{{ $ad->user->name }}</a>
                        @else
                            Неизвестный продавец
                        @endif
                    </p>
                    <p><strong>Контакты:</strong> {{ $ad->contact }}</p>
                    @auth
                        <div class="ad-actions">
                            @if ($ad->user_id === auth()->id())
                                <a href="{{ route('ad.edit', $ad->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>Редактировать
                                </a>
                                <form 
                                    action="{{ route('ad.destroy', $ad->id) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Вы уверены, что хотите удалить объявление?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>Удалить
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('chat.start', $ad->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-envelope"></i> Написать продавцу
                                    </button>
                                </form>
                                <button id="favorite-btn" class="btn btn-outline-secondary {{ auth()->user()->favorites()->where('ad_id', $ad->id)->exists() ? 'favorited' : '' }}" data-ad-id="{{ $ad->id }}">
                                    <i class="fas fa-heart"></i> <span>{{ auth()->user()->favorites()->where('ad_id', $ad->id)->exists() ? 'В избранном' : 'В избранное' }}</span>
                                </button>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

        <script>
            document.getElementById('favorite-btn')?.addEventListener('click', function() {
                const adId = this.dataset.adId;
                const button = this;
                fetch('{{ route('favorites.toggle', ':adId') }}'.replace(':adId', adId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.classList.toggle('favorited');
                        button.querySelector('span').textContent = data.is_favorited ? 'В избранном' : 'В избранное';
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            function changeMainImage(src, thumbnail) {
                document.getElementById('main-image').src = src;
                document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
                thumbnail.classList.add('active');
            }
        </script>

@endsection