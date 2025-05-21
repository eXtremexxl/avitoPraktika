@extends('admin.layouts.app')
@section('title', 'Пользователи')
@section('content')
    <h1>Пользователи</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}">Редактировать</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection