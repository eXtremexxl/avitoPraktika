@extends('layouts.app')
@section('title', 'Редактировать профиль')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
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

        <h1><i class="fas fa-user-edit"></i> Редактировать профиль</h1>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Имя:</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required>
                @error('phone')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="avatar">Аватарка:</label>
                <div class="avatar-preview">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Аватар" class="avatar-img">
                    @else
                        <i class="fas fa-user-circle default-avatar"></i>
                    @endif
                </div>
                <input type="file" name="avatar" id="avatar" accept="image/*">
                @error('avatar')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Новый пароль (оставьте пустым, если не меняете):</label>
                <input type="password" name="password" id="password">
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Подтверждение пароля:</label>
                <input type="password" name="password_confirmation" id="password_confirmation">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Сохранить
            </button>
        </form>
    </div>


        <script>
            document.getElementById('avatar').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.querySelector('.avatar-preview');
                        preview.innerHTML = `<img src="${e.target.result}" alt="Предпросмотр" class="avatar-img">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>

@endsection