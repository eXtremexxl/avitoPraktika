@extends('admin.layouts.app')
@section('title', 'Редактировать пользователя')
@section('content')
    <h1>Редактировать пользователя</h1>
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email') <span class="error">{{ $message }}</span> @enderror
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required>
        @error('phone') <span class="error">{{ $message }}</span> @enderror
        <select name="role" required>
            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Пользователь</option>
            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Админ</option>
        </select>
        @error('role') <span class="error">{{ $message }}</span> @enderror
        <button type="submit">Сохранить</button>
    </form>
@endsection