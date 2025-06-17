@extends('admin.layouts.app')
@section('title', 'Объявления')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/ads.css') }}">
@endsection
@section('content')
    <h1>Объявления</h1>
    <div class="actions">
        <form method="GET" action="{{ route('admin.ads') }}" class="search-form">
            <input type="text" name="search" placeholder="Поиск по названию..." value="{{ request('search') }}">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        <form id="filterForm" method="GET" action="{{ route('admin.ads') }}">
            <select name="status" onchange="this.form.submit()">
                <option value="">Все статусы</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активно</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивно</option>
            </select>
        </form>
    </div>
    @if ($ads->isEmpty())
        <p>Объявления не найдены.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Описание</th>
                    <th>Контакты</th>
                    <th>Статус</th>
                    <th>Подтверждено</th>
                    <th>Фотографии</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ads as $ad)
                    <tr>
                        <td>{{ $ad->id }}</td>
                        <td>{{ $ad->title }}</td>
                        <td>{{ $ad->category->name }}</td>
                        <td>{{ $ad->price }}</td>
                        <td>{{ Str::limit($ad->description, 50) }}</td>
                        <td>{{ $ad->contact }}</td>
                        <td>{{ $ad->is_active ? 'Активно' : 'Неактивно' }}</td>
                        <td>{{ $ad->approved ? 'Да' : 'Нет' }}</td>
                        <td>
                            <div class="photo-preview">
                                @if ($ad->photos->isNotEmpty())
                                    @foreach ($ad->photos as $photo)
                                        <img src="{{ asset('storage/' . $photo->path) }}" alt="Фото">
                                    @endforeach
                                @else
                                    <span>Нет фото</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if (!$ad->approved)
                                <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-approve"><i class="fas fa-check"></i> Подтвердить</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" class="delete-form" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete"><i class="fas fa-trash"></i> Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $ads->links() }}
    @endif
@endsection