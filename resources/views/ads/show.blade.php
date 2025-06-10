@extends('layouts.app')

@section('title', $ad->title)

@push('meta')
    <meta property="og:title" content="{{ $ad->title }}">
    <meta property="og:description" content="{{ Str::limit($ad->description, 200) }}">
    <meta property="og:image" content="{{ $ad->photos->isNotEmpty() ? asset('storage/' . $ad->photos->first()->path) : asset('images/placeholder.jpg') }}">
    <meta property="og:url" content="{{ route('ad.show', $ad->id) }}">
    <meta property="og:type" content="product">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ad-show.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
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
                                aria-label="Основное фото объявления"
                                onclick="openLightbox(0)"
                            >
                            <div class="photo-loading"></div>
                        </div>
                        @if ($ad->photos->count() > 1)
                            <div class="thumbnails">
                                @foreach ($ad->photos as $index => $photo)
                                    <img 
                                        src="{{ asset('storage/' . $photo->path) }}" 
                                        alt="Миниатюра {{ $ad->title }} #{{ $index + 1 }}" 
                                        class="{{ $photo->is_main ? 'active' : '' }}" 
                                        onclick="changeMainImage('{{ asset('storage/' . $photo->path) }}', this); openLightbox({{ $index }})"
                                        loading="lazy"
                                        aria-label="Миниатюра фото {{ $index + 1 }}"
                                    >
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="no-photos">Нет фотографий</div>
                    @endif
                </div>
                <div class="ad-info">
                    <div class="ad-info-item">
                        <i class="fas fa-ruble-sign"></i>
                        <span class="price">{{ number_format($ad->price, 0, '.', ' ') }} руб.</span>
                    </div>
                    <div class="ad-info-item">
                        <i class="fas fa-tags"></i>
                        <span><strong>Категория:</strong> {{ $ad->category->name }}</span>
                    </div>
                    <div class="ad-info-item">
                        <i class="fas fa-align-left"></i>
                        <span><strong>Описание:</strong> {{ $ad->description }}</span>
                    </div>
                    <div class="ad-info-item">
                        <i class="fas fa-user"></i>
                        <span><strong>Продавец:</strong> 
                            @if ($ad->user)
                                <a href="{{ route('profile.show', $ad->user->id) }}">{{ $ad->user->name }}</a>
                            @else
                                Неизвестный продавец
                            @endif
                        </span>
                    </div>
                    <div class="ad-info-item">
                        <i class="fas fa-phone"></i>
                        <span><strong>Контакты:</strong> {{ $ad->contact }}</span>
                    </div>
                    <div class="ad-info-item stats">
                        <i class="fas fa-eye"></i>
                        <span><strong>Просмотры:</strong> {{ $ad->views()->count() }}</span>
                    </div>
                    <div class="ad-info-item stats">
                        <i class="fas fa-heart"></i>
                        <span><strong>В избранном:</strong> {{ $ad->favorites()->count() }} раз</span>
                    </div>
                    <div class="ad-info-item stats">
                        <i class="fas fa-calendar"></i>
                        <span><strong>Опубликовано:</strong> {{ $ad->created_at->format('d.m.Y') }}</span>
                    </div>
                    @auth
                        <div class="ad-actions">
                            @if ($ad->user_id === auth()->id())
                                <a href="{{ route('ad.edit', $ad->id) }}" class="btn btn-primary" aria-label="Редактировать объявление">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>
                                <form 
                                    action="{{ route('ad.destroy', $ad->id) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Вы уверены, что хотите удалить объявление?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" aria-label="Удалить объявление">
                                        <i class="fas fa-trash"></i> Удалить
                                    </button>
                                </form>
                            @else
                                @if (auth()->check() && auth()->id() !== $ad->user_id)
                                    <a href="{{ route('chat.start', $ad->id) }}" class="btn btn-primary" aria-label="Написать продавцу">
                                        <i class="fas fa-envelope"></i> Написать продавцу
                                    </a>
                                @endif
                                <button 
                                    id="favorite-btn" 
                                    class="btn btn-outline-secondary {{ auth()->user()->favorites()->where('ad_id', $ad->id)->exists() ? 'favorited' : '' }}" 
                                    data-ad-id="{{ $ad->id }}"
                                    aria-label="{{ auth()->user()->favorites()->where('ad_id', $ad->id)->exists() ? 'Удалить из избранного' : 'Добавить в избранное' }}"
                                >
                                    <i class="fas fa-heart"></i> 
                                    <span>{{ auth()->user()->favorites()->where('ad_id', $ad->id)->exists() ? 'В избранном' : 'В избранное' }}</span>
                                </button>
                                <button class="btn btn-outline-secondary share-btn" onclick="shareAd()" aria-label="Поделиться объявлением">
                                    <i class="fas fa-share-alt"></i> Поделиться
                                </button>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        @if ($ad->user && $ad->user->reviewsReceived->isNotEmpty())
            <div class="seller-reviews">
                <h2><i class="fas fa-star"></i> Отзывы о продавце ({{ number_format($ad->user->averageRating(), 1) }}/5)</h2>
                <div class="reviews-list">
                    @foreach ($ad->user->reviewsReceived->take(3) as $review)
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
                <a href="{{ route('profile.show', $ad->user->id) }}#reviews" class="btn btn-primary1">
                    <i class="fas fa-star"></i> Все отзывы
                </a>
            </div>
        @endif

        @if ($relatedAds->isNotEmpty())
            <div class="related-ads">
                <h2><i class="fas fa-bullhorn"></i> Похожие объявления</h2>
                <div class="ads-grid">
                    @foreach ($relatedAds->take(4) as $relatedAd)
                        <div class="ad-card">
                            <div class="ad-image">
                                @if ($relatedAd->photos->isNotEmpty())
                                    <img src="{{ asset('storage/' . $relatedAd->photos->first()->path) }}" alt="{{ $relatedAd->title }}" loading="lazy">
                                @else
                                    <div class="no-image">Нет фото</div>
                                @endif
                            </div>
                            <div class="ad-info">
                                <h3><a href="{{ route('ad.show', $relatedAd->id) }}">{{ $relatedAd->title }}</a></h3>
                                <p class="price">{{ number_format($relatedAd->price, 0, '.', ' ') }} руб.</p>
                                <p class="category">{{ $relatedAd->category->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Лайтбокс -->
    <dialog id="lightbox" class="lightbox">
        <div class="lightbox-content">
            <img id="lightbox-image" src="" alt="Полноэкранное фото">
            @if ($ad->photos->count() > 1)
                <button class="lightbox-nav prev" aria-label="Предыдущее фото" onclick="navigateLightbox(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="lightbox-nav next" aria-label="Следующее фото" onclick="navigateLightbox(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @endif
            <button class="close-lightbox" aria-label="Закрыть лайтбокс">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </dialog>

<script>
    // Массив фотографий для навигации
    const photos = [
        @foreach ($ad->photos as $photo)
            '{{ asset('storage/' . $photo->path) }}',
        @endforeach
    ];
    let currentPhotoIndex = 0;

    // Обработчик для избранного
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
                button.setAttribute('aria-label', data.is_favorited ? 'Удалить из избранного' : 'Добавить в избранное');
            } else {
                alert('Ошибка при изменении избранного');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка');
        });
    });

    // Смена главного изображения
    function changeMainImage(src, thumbnail) {
        const mainImage = document.getElementById('main-image');
        mainImage.src = src;
        mainImage.classList.add('loading');
        mainImage.onload = () => mainImage.classList.remove('loading');
        document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    // Открытие лайтбокса
    function openLightbox(index) {
        currentPhotoIndex = index;
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        lightboxImage.src = photos[currentPhotoIndex];
        lightbox.showModal();
    }

    // Навигация по фотографиям
    function navigateLightbox(direction) {
        currentPhotoIndex = (currentPhotoIndex + direction + photos.length) % photos.length;
        const lightboxImage = document.getElementById('lightbox-image');
        lightboxImage.src = photos[currentPhotoIndex];
    }

    // Закрытие лайтбокса
    const lightbox = document.getElementById('lightbox');
    const closeButton = document.querySelector('.close-lightbox');
    
    closeButton?.addEventListener('click', () => {
        lightbox.close();
    });

    // Закрытие по клику на фон
    lightbox?.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            lightbox.close();
        }
    });

    // Закрытие по клавише Esc
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.open) {
            lightbox.close();
        }
    });

    // Навигация по стрелкам клавиатуры
    document.addEventListener('keydown', (event) => {
        if (lightbox.open) {
            if (event.key === 'ArrowLeft') {
                navigateLightbox(-1);
            } else if (event.key === 'ArrowRight') {
                navigateLightbox(1);
            }
        }
    });

    // Поделиться
    function shareAd() {
        const url = window.location.href;
        const title = '{{ $ad->title }}';
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(console.error);
        } else {
            const shareOptions = [
                { name: 'Копировать ссылку', action: () => {
                    navigator.clipboard.writeText(url).then(() => alert('Ссылка скопирована!'));
                }},
                { name: 'Telegram', action: () => window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`) },
                { name: 'WhatsApp', action: () => window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(title + ' ' + url)}`) }
            ];
            const choice = prompt('Выберите способ поделиться:\n1. Копировать ссылку\n2. Telegram\n3. WhatsApp', '1');
            if (choice && shareOptions[parseInt(choice) - 1]) {
                shareOptions[parseInt(choice) - 1].action();
            }
        }
    }
</script>
@endsection