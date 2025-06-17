@extends('admin.layouts.app')
@section('title', 'Пользователи')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/users.css') }}">
@endsection
@section('content')
    <h1>Пользователи</h1>
    <div class="actions">
        <form method="GET" action="{{ route('admin.users') }}" class="search-form">
            <input type="text" name="search" placeholder="Поиск по имени или email..." value="{{ request('search') }}">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    @if ($users->isEmpty())
        <p>Пользователи не найдены.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-edit"><i class="fas fa-edit"></i> Редактировать</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="delete-form" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete"><i class="fas fa-trash"></i> Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    @endif
@endsection