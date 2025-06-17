@extends('admin.layouts.app')
@section('title', 'Категории')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/categories.css') }}">
@endsection
@section('content')
    <h1>Категории</h1>
    <div class="form-card">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="form-group">
                <label>Название категории</label>
                <input type="text" name="name" placeholder="Введите название" required>
                @error('name') <span class="error">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Добавить</button>
        </form>
    </div>
    @if ($categories->isEmpty())
        <p>Категории не найдены.</p>
    @else
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
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="delete-form" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete"><i class="fas fa-trash"></i> Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection