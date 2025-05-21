@extends('layouts.app')
@section('title', 'Чат: ' . $chat->ad->title)
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
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

        <h1>Чат по объявлению: <a href="{{ route('ad.show', $chat->ad->id) }}">{{ $chat->ad->title }}</a></h1>
        <div class="chat-container">
            <div class="chat-messages" id="chat-messages">
                @foreach ($chat->messages as $message)
                    <div class="message {{ $message->sender_id === auth()->id() ? 'message-sent' : 'message-received' }}">
                        <div class="message-content">
                            @if ($message->content)
                                <p>{{ $message->content }}</p>
                            @endif
                            @if ($message->files->isNotEmpty())
                                <div class="message-files">
                                    @foreach ($message->files as $file)
                                        @if (in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif']))
                                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $file->path) }}" alt="{{ $file->original_name }}" class="file-preview">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="file-link">
                                                <i class="fas fa-file"></i> {{ $file->original_name }} ({{ number_format($file->size / 1024, 2) }} KB)
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            <span class="message-time">{{ $message->created_at->format('d.m.Y H:i') }}</span>
                            @if ($message->sender_id === auth()->id())
                                <span class="message-status">
                                    <i class="fas {{ $message->is_read ? 'fa-check-double' : 'fa-check' }}"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <form id="message-form" enctype="multipart/form-data">
                @csrf
                <div class="message-input">
                    <textarea name="content" placeholder="Введите сообщение..." rows="3"></textarea>
                    <div class="message-actions">
                        <label for="files" class="file-upload">
                            <i class="fas fa-paperclip"></i> Прикрепить файл
                            <input type="file" name="files[]" id="files" multiple accept="image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Отправить
                        </button>
                    </div>
                    <div id="form-errors" class="form-errors" style="display: none;"></div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Функция для прокрутки к последнему сообщению
        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        // Прокрутка при загрузке страницы
        window.addEventListener('load', function() {
            scrollToBottom();
        });

        // Обработка отправки формы
        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const errorDiv = document.getElementById('form-errors');

            fetch('{{ route('chat.send', $chat->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    form.querySelector('textarea').value = '';
                    form.querySelector('input[type="file"]').value = '';
                    errorDiv.style.display = 'none';
                    errorDiv.innerHTML = '';
                    location.reload(); // Перезагрузка страницы
                } else {
                    errorDiv.style.display = 'block';
                    errorDiv.innerHTML = data.error ? Object.values(data.error).flat().join('<br>') : 'Произошла ошибка';
                    scrollToBottom(); // Прокрутка при ошибке
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = 'Не удалось отправить сообщение';
                scrollToBottom(); // Прокрутка при ошибке
            });
        });

        // Прокрутка после перезагрузки страницы
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
        });
    </script>
@endsection