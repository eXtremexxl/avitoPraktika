<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/admin/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Админка</h2>
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}" class="@if(Route::currentRouteName() == 'admin.dashboard') active @endif"><i class="fas fa-tachometer-alt"></i> Панель управления</a></li>
                    <li><a href="{{ route('admin.users') }}" class="@if(Route::currentRouteName() == 'admin.users') active @endif"><i class="fas fa-users"></i> Пользователи</a></li>
                    <li><a href="{{ route('admin.categories') }}" class="@if(Route::currentRouteName() == 'admin.categories') active @endif"><i class="fas fa-tags"></i> Категории</a></li>
                    <li><a href="{{ route('admin.ads') }}" class="@if(Route::currentRouteName() == 'admin.ads') active @endif"><i class="fas fa-ad"></i> Объявления</a></li>
                    <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('expanded');
        });
        document.querySelectorAll('form.delete-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!confirm('Вы уверены, что хотите удалить?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>