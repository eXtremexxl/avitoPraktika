@extends('admin.layouts.app')
@section('title', 'Объявления')
@section('content')
    <h1>Объявления</h1>
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
                    <td>{{ $ad->description }}</td>
                    <td>{{ $ad->contact }}</td>
                    <td>{{ $ad->is_active ? 'Активно' : 'Неактивно' }}</td>
                    <td>{{ $ad->approved ? 'Да' : 'Нет' }}</td>
                    <td>
                        @if ($ad->photos->isNotEmpty())
                            @foreach ($ad->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->path) }}" alt="Фото" style="max-width: 50px; margin: 2px;">
                            @endforeach
                        @else
                            Нет фото
                        @endif
                    </td>
                    <td>
                        @if (!$ad->approved)
                            <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit">Подтвердить</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" style="display:inline;">
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