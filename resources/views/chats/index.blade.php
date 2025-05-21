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
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <h1>Мои чаты</h1>
        <div class="chat-filters">
            <form method="GET" action="{{ route('chats.index') }}">
                <div class="filter-group">
                    <input type="text" name="ad_title" placeholder="Название объявления" value="{{ request('ad_title') }}">
                </div>
                <div class="filter-group">
                    <input type="text" name="partner_name" placeholder="Имя собеседника" value="{{ request('partner_name') }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Фильтровать
                </button>
            </form>
        </div>
        @if ($chats->isEmpty())
            <p class="no-results">Нет активных чатов.</p>
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
                                <div class="no-image">Нет фото</div>
                            @endif
                        </div>
                        <div class="chat-details">
                            <h3>
                                <a href="{{ route('chat.show', $chat->id) }}">
                                    {{ $chat->ad->title }}
                                </a>
                            </h3>
                            <p class="chat-partner">
                                <strong>Собеседник:</strong>
                                {{ $chat->seller_id === auth()->id() ? $chat->buyer->name : $chat->seller->name }}
                            </p>
                            <p class="chat-last-message">
                                {{ $chat->messages->last()->content ?? 'Нет сообщений' }}
                            </p>
                        </div>
                        <div class="chat-meta">
                            <span class="chat-time">
                                {{ $chat->messages->last()->created_at->format('d.m.Y H:i') ?? '-' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection