@extends('layouts.app')
@section('title', 'Добавить объявление')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ad-form.css') }}">
@endpush
@section('content')
    <div class="container">
        <h1>Добавить объявление</h1>
        <form method="POST" action="{{ route('ad.store') }}" enctype="multipart/form-data" class="ad-form">
            @csrf
            <div class="form-group">
                <label for="title">Название</label>
                <input type="text" name="title" id="title" placeholder="Название объявления" value="{{ old('title') }}" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Категория</label>
                <select name="category_id" id="category_id" required>
                    <option value="">Выберите категорию</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="price">Цена (руб.)</label>
                <input type="number" name="price" id="price" placeholder="0.00" step="0.01" value="{{ old('price') }}" required>
                @error('price') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea name="description" id="description" placeholder="Опишите ваше объявление" required>{{ old('description') }}</textarea>
                @error('description') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="contact">Контакты</label>
                <input type="text" name="contact" id="contact" placeholder="Email или телефон" value="{{ old('contact') }}" required>
                @error('contact') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                    Активно
                </label>
            </div>

            <div class="form-group photo-upload">
                <label for="photos">Фотографии (до 5)</label>
                <input type="file" name="photos[]" id="photos" multiple accept="image/*">
                @error('photos.*') <span class="error">{{ $message }}</span> @enderror
                @error('photos') <span class="error">{{ $message }}</span> @enderror
                <div id="photo-preview" class="photo-preview"></div>

                <label for="main_photo">Основная фотография</label>
                <select name="main_photo" id="main-photo"></select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Создать</button>
                <a href="{{ route('home') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        const photosInput = document.getElementById('photos');
        const preview = document.getElementById('photo-preview');
        const mainPhotoSelect = document.getElementById('main-photo');

        function updateMainPhotoOptions(files) {
            mainPhotoSelect.innerHTML = '';
            files.forEach((file, i) => {
                const option = document.createElement('option');
                option.value = i;
                option.text = `Фотография ${i + 1}`;
                mainPhotoSelect.appendChild(option);
            });
            mainPhotoSelect.value = "{{ old('main_photo', 0) }}" < files.length ? "{{ old('main_photo', 0) }}" : 0;
        }

        let selectedFiles = [];

        photosInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            if (selectedFiles.length + files.length > 5) {
                alert('Максимум 5 фотографий!');
                e.target.value = '';
                return;
            }

            selectedFiles = [...selectedFiles, ...files];
            renderPhotos();
            updateMainPhotoOptions(selectedFiles);
        });

        function renderPhotos() {
            preview.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const container = document.createElement('div');
                container.className = 'photo-item';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'Предпросмотр';

                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-photo';
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = () => {
                    selectedFiles.splice(index, 1);
                    renderPhotos();
                    updateMainPhotoOptions(selectedFiles);
                };

                container.appendChild(img);
                container.appendChild(removeBtn);
                preview.appendChild(container);
            });

            // Обновляем input.files
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            photosInput.files = dataTransfer.files;
        }

        // Инициализация при ошибке валидации
        document.addEventListener('DOMContentLoaded', () => {
            updateMainPhotoOptions(selectedFiles);
        });
    </script>
@endsection