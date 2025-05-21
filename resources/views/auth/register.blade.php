@extends('layouts.app')
@section('title', 'Регистрация')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
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

        <div class="auth-form">
            <h1>Регистрация</h1>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="name">ФИО</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        placeholder="Введите ФИО" 
                        value="{{ old('name') }}" 
                        required
                    >
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        placeholder="Введите email" 
                        value="{{ old('email') }}" 
                        required
                    >
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        placeholder="Введите телефон" 
                        value="{{ old('phone') }}" 
                        required
                    >
                    @error('phone') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Введите пароль" 
                        required 
                        autocomplete="new-password"
                    >
                    @error('password') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Подтверждение пароля</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        placeholder="Подтвердите пароль" 
                        required 
                        autocomplete="new-password"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </div>
            </form>
            <p class="auth-link">
                Уже есть аккаунт? <a href="{{ route('login.form') }}">Войти</a>
            </p>
        </div>
    </div>
@endsection