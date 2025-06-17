@extends('admin.layouts.app')
@section('title', 'Редактировать пользователя')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/edit_user.css') }}">
@endsection
@section('content')
    <h1>Редактировать пользователя</h1>
    <div class="form-card">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label>ФИО</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required>
                    @error('phone') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Роль</label>
                    <select name="role" required>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Пользователь</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Админ</option>
                    </select>
                    @error('role') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Сохранить</button>
        </form>
    </div>
@endsection