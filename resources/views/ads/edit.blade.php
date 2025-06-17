@extends('layouts.app')
@section('title', 'Редактировать объявление')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ad-form.css') }}">
@endpush
@section('content')
    <div class="container">
        <h1>Редактировать объявление</h1>
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-error">
                Пожалуйста, исправьте ошибки:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('ad.update', $ad->id) }}" enctype="multipart/form-data" class="ad-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">Название</label>
                <input type="text" name="title" id="title" placeholder="Название объявления" value="{{ old('title', $ad->title) }}" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Категория</label>
                <select name="category_id" id="category_id" required>
                    <option value="">Выберите категорию</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $ad->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="price">Цена (руб.)</label>
                <input type="number" name="price" id="price" placeholder="0.00" step="0.01" value="{{ old('price', $ad->price) }}" required>
                @error('price') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea name="description" id="description" placeholder="Опишите ваше объявление" required>{{ old('description', $ad->description) }}</textarea>
                @error('description') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="contact">Контакты</label>
                <input type="text" name="contact" id="contact" placeholder="Email или телефон" value="{{ old('contact', $ad->contact) }}" required>
                @error('contact') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" {{ old('is_active', $ad->is_active) ? 'checked' : '' }}>
                    Активно
                </label>
                @error('is_active') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group photo-upload">
                <label>Текущие фотографии ({{ $ad->photos->count() }}/5)</label>
                <div id="existing-photo-preview" class="photo-preview">
                    @foreach ($ad->photos as $index => $photo)
                        <div class="photo-item" data-id="{{ $photo->id }}">
                            <img src="{{ asset('storage/' . $photo->path) }}" alt="Фото">
                            <button type="button" class="remove-photo" onclick="removeExistingPhoto({{ $photo->id }})">×</button>
                        </div>
                    @endforeach
                </div>

                <label for="photos">Добавить или заменить фотографии (до 5)</label>
                <input type="file" name="photos[]" id="photos" multiple accept="image/*">
                @error('photos.*') <span class="error">{{ $message }}</span> @enderror
                @error('photos') <span class="error">{{ $message }}</span> @enderror
                <div id="new-photo-preview" class="photo-preview"></div>

                <label for="main_photo">Основная фотография</label>
                <select name="main_photo" id="main-photo">
                    <option value="">Без основной фотографии</option>
                </select>
                @error('main_photo') <span class="error">{{ $message }}</span> @enderror

                <input type="hidden" name="delete_photos" id="delete-photos" value="">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="{{ route('ad.show', $ad->id) }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        const photosInput = document.getElementById('photos');
        const existingPreview = document.getElementById('existing-photo-preview');
        const newPreview = document.getElementById('new-photo-preview');
        const mainPhotoSelect = document.getElementById('main-photo');
        const deletePhotosInput = document.getElementById('delete-photos');

        let existingPhotos = @json($ad->photos->pluck('id'));
        let newFiles = [];
        let deletedPhotos = [];

        function updateMainPhotoOptions() {
            mainPhotoSelect.innerHTML = '<option value="">Без основной фотографии</option>';
            existingPhotos.forEach((id, i) => {
                const option = document.createElement('option');
                option.value = i;
                option.text = `Текущее фото ${i + 1}`;
                mainPhotoSelect.appendChild(option);
            });
            newFiles.forEach((file, i) => {
                const option = document.createElement('option');
                option.value = existingPhotos.length + i;
                option.text = `Новое фото ${i + 1}`;
                mainPhotoSelect.appendChild(option);
            });
            const mainPhotoIndex = @json($ad->photos->where('is_main', true)->first()->id ?? null);
            const oldValue = "{{ old('main_photo') }}";
            if (oldValue !== '' && parseInt(oldValue) < (existingPhotos.length + newFiles.length)) {
                mainPhotoSelect.value = oldValue;
            } else if (mainPhotoIndex !== null && existingPhotos.includes(mainPhotoIndex)) {
                mainPhotoSelect.value = existingPhotos.indexOf(mainPhotoIndex);
            } else if (existingPhotos.length + newFiles.length > 0) {
                mainPhotoSelect.value = 0;
            }
        }

        function removeExistingPhoto(photoId) {
            deletedPhotos.push(photoId);
            existingPhotos = existingPhotos.filter(id => id !== photoId);
            deletePhotosInput.value = deletedPhotos.join(',') || '';
            renderExistingPhotos();
            updateMainPhotoOptions();
        }

        function renderExistingPhotos() {
            existingPreview.innerHTML = '';
            existingPhotos.forEach((id, index) => {
                const photo = @json($ad->photos->toArray()).find(p => p.id === id);
                if (photo) {
                    const container = document.createElement('div');
                    container.className = 'photo-item';
                    container.dataset.id = id;

                    const img = document.createElement('img');
                    img.src = '{{ asset('storage') }}/' + photo.path;
                    img.alt = 'Фото';

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-photo';
                    removeBtn.innerHTML = '×';
                    removeBtn.onclick = () => removeExistingPhoto(id);

                    container.appendChild(img);
                    container.appendChild(removeBtn);
                    existingPreview.appendChild(container);
                }
            });
        }

        photosInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            if (existingPhotos.length + newFiles.length + files.length > 5) {
                alert('Максимум 5 фотографий!');
                e.target.value = '';
                return;
            }

            newFiles = [...newFiles, ...files];
            renderNewPhotos();
            updateMainPhotoOptions();

            const dataTransfer = new DataTransfer();
            newFiles.forEach(file => dataTransfer.items.add(file));
            photosInput.files = dataTransfer.files;
        });

        function renderNewPhotos() {
            newPreview.innerHTML = '';
            newFiles.forEach((file, index) => {
                const container = document.createElement('div');
                container.className = 'photo-item';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'Предпросмотр';

                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-photo';
                removeBtn.innerHTML = '×';
                removeBtn.onclick = () => {
                    newFiles.splice(index, 1);
                    renderNewPhotos();
                    updateMainPhotoOptions();
                    const dataTransfer = new DataTransfer();
                    newFiles.forEach(f => dataTransfer.items.add(f));
                    photosInput.files = dataTransfer.files;
                };

                container.appendChild(img);
                container.appendChild(removeBtn);
                newPreview.appendChild(container);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            deletePhotosInput.value = ''; 
            renderExistingPhotos();
            updateMainPhotoOptions();
        });
    </script>
@endsection