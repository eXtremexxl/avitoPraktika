<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="{{ route('home') }}" class="logo">GoSell</a>
            <nav class="nav">
                @auth
                    <a href="{{ route('ad.create') }}">Добавить объявление</a>
                    <a href="{{ route('chats.index') }}">
                        Чаты
                        @if ($unreadCount > 0)
                            <span class="unread-count">{{ $unreadCount }}</span>
                        @endif
                    </a>
                <a href="{{ route('favorites.index') }}"> Избранное </a>
                </a>

                    <a href="{{ route('profile.index') }}">Личный кабинет</a>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}">Админка</a>
                    @endif
                    <a href="{{ route('logout') }}">Выйти</a>
                @else
                    <a href="{{ route('login.form') }}">Войти</a>
                    <a href="{{ route('register.form') }}">Регистрация</a>
                @endauth
            </nav>
            <button class="menu-toggle" aria-label="Открыть меню">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12H21M3 6H21M3 18H21" stroke="#333" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    <script>
        document.querySelector('.menu-toggle')?.addEventListener('click', () => {
            document.querySelector('.nav').classList.toggle('active');
        });
    </script>
    
</body>
</html>