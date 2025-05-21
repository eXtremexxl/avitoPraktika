<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Админка</a>
            <a href="{{ route('admin.users') }}">Пользователи</a>
            <a href="{{ route('admin.categories') }}">Категории</a>
            <a href="{{ route('admin.ads') }}">Объявления</a>
            <a href="/mode?mode=logout">Выйти</a>
        </nav>
    </header>
    <main>
        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>