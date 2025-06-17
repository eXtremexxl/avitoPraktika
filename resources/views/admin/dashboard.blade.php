@extends('admin.layouts.app')
@section('title', 'Панель управления')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endsection
@section('content')
    <h1>Панель управления</h1>
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3>Пользователи</h3>
            <p>{{ $userCount ?? 100 }}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-ad"></i>
            <h3>Объявления</h3>
            <p>{{ $adCount ?? 250 }}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-tags"></i>
            <h3>Категории</h3>
            <p>{{ $categoryCount ?? 30 }}</p>
        </div>
    </div>
    <div class="welcome-card">
        <h2>Добро пожаловать в админку!</h2>
        <p>Управляйте пользователями, категориями и объявлениями с удобным интерфейсом.</p>
    </div>
@endsection