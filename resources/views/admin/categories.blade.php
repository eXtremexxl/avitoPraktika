@extends('admin.layouts.app')
@section('title', 'Категории')
@section('content')
    <h1>Категории</h1>
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Название категории" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror
        <button type="submit">Добавить</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
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