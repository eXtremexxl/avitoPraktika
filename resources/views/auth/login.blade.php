@extends('layouts.app')
@section('title', 'Вход')
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
            <h1>Вход</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        placeholder="Введите email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="username"
                    >
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Введите пароль" 
                        required 
                        autocomplete="current-password"
                    >
                    @error('password') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Войти</button>
                </div>
            </form>
            <p class="auth-link">
                Нет аккаунта? <a href="{{ route('register.form') }}">Зарегистрироваться</a>
            </p>
        </div>
    </div>
@endsection