<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', [AdController::class, 'index'])->name('home');

// Регистрация
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Вход
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Выход
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Категории
Route::get('/category/{id}', [AdController::class, 'showCategory'])->name('category.show');

// Объявления (авторизованные пользователи)
Route::middleware('auth')->group(function () {
    // Создание объявления
    Route::get('/ad/create', [AdController::class, 'create'])->name('ad.create');
    Route::post('/ad', [AdController::class, 'store'])->name('ad.store');
    Route::get('/ad/{id}/edit', [AdController::class, 'edit'])->name('ad.edit');
    Route::put('/ad/{id}', [AdController::class, 'update'])->name('ad.update');
    Route::delete('/ad/{id}', [AdController::class, 'destroy'])->name('ad.destroy');

    // Личный кабинет
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Избранное
    Route::post('/favorites/{ad}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
});

// Детальная страница объявления
Route::get('/ad/{id}', [AdController::class, 'show'])->name('ad.show');

// Профиль пользователя
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

// Чаты
Route::middleware('auth')->group(function () {
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{adId}/start', [ChatController::class, 'start'])->name('chat.start');
    Route::get('/chats/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chats/{chatId}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chats/{chatId}/delete', [ChatController::class, 'delete'])->name('chat.delete');
    
});

// Отзывы
Route::post('/reviews/{user}', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');

// Админка
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');
    Route::get('/ads', [AdminController::class, 'ads'])->name('admin.ads');
    Route::put('/ads/{id}/approve', [AdminController::class, 'approveAd'])->name('admin.ads.approve');
    Route::delete('/ads/{id}', [AdminController::class, 'destroyAd'])->name('admin.ads.destroy');
});