@extends('layouts.app')
@section('title', 'Мои чаты')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chats-index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <h1>Мои чаты</h1>
        <div class="chat-filters">
            <form method="GET" action="{{ route('chats.index') }}" id="chat-filters-form">
                <div class="filter-group">
                    <input type="text" name="ad_title" placeholder="Название объявления" value="{{ request('ad_title') }}">
                </div>
                <div class="filter-group">
                    <input type="text" name="partner_name" placeholder="Имя собеседника" value="{{ request('partner_name') }}">
                </div>
                <div class="filter-group">
                    <select name="sort" onchange="this.form.submit()">
                        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>По убыванию даты</option>
                        <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>По возрастанию даты</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Применить
                </button>
            </form>
        </div>
        @if ($chats->isEmpty())
            <p class="no-results">Нет чатов по заданным фильтрам.</p>
        @else
            <div class="chats-list">
                @foreach ($chats as $chat)
                    <div class="chat-item {{ $chat->unread_messages_count > 0 ? 'unread' : '' }}">
                        <div class="chat-icon">
                            <i class="fas fa-envelope"></i>
                            @if ($chat->unread_messages_count > 0)
                                <span class="unread-count">{{ $chat->unread_messages_count }}</span>
                            @endif
                        </div>
                        <div class="chat-image">
                            @if ($photo = ($chat->ad->photos->where('is_main', true)->first() ?? $chat->ad->photos->first()))
                                <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $chat->ad->title }}" loading="lazy">
                            @else
                                <div class="no-image"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div class="chat-details">
                            <div class="chat-partner-info">
                                @if ($partner = ($chat->seller_id === auth()->id() ? $chat->buyer : $chat->seller))
                                    <div class="partner-avatar">
                                        @if ($partner->avatar)
                                            <img src="{{ asset('storage/' . $partner->avatar) }}" alt="{{ $partner->name }}" loading="lazy">
                                        @else
                                            <div class="no-avatar"><i class="fas fa-user"></i></div>
                                        @endif
                                    </div>
                                    <span class="partner-name">{{ $partner->name }}</span>
                                @endif
                            </div>
                            <h3><a href="{{ route('chat.show', $chat->id) }}">{{ $chat->ad->title }}</a></h3>
                            <p class="chat-last-message">
                                {{ $chat->messages->last()->content ?? 'Нет сообщений' }}
                            </p>
                        </div>
                        <div class="chat-meta">
                            <span class="chat-time">
                                {{ optional($chat->messages->last())->created_at?->format('d.m.Y H:i') ?? '-' }}
                            </span>
                            <button class="btn btn-danger btn-delete" data-chat-id="{{ $chat->id }}" title="Удалить чат">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Вы уверены, что хотите удалить этот чат? Он будет скрыт только для вас.')) {
                    const chatId = this.getAttribute('data-chat-id');
                    fetch('{{ url('chats') }}/' + chatId + '/delete', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.error || 'Ошибка при удалении чата');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Не удалось удалить чат: ' + error.message);
                    });
                }
            });
        });

        document.getElementById('chat-filters-form').addEventListener('input', function() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.submit(), 500);
        });
    </script>
@endsection