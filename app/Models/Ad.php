<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель объявления.
 *
 * @property int $id
 * @property string $title
 * @property int $category_id
 * @property float $price
 * @property string $description
 * @property string $contact
 * @property bool $is_active
 * @property bool $approved
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read string $formatted_price
 * @property-read User $user
 * @property-read Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|Photo[] $photos
 * @property-read \Illuminate\Database\Eloquent\Collection|Chat[] $chats
 * @property-read \Illuminate\Database\Eloquent\Collection|View[] $views
 * @property-read \Illuminate\Database\Eloquent\Collection|Favorite[] $favorites
 */
class Ad extends Model
{
    /**
     * Атрибуты, доступные для массового заполнения.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'category_id',
        'price',
        'description',
        'contact',
        'is_active',
        'approved',
        'user_id',
    ];

    /**
     * Атрибуты, приводимые к определённым типам.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
        'approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Загрузка связей по умолчанию для оптимизации.
     *
     * @var array<string>
     */
    protected $with = ['photos'];

    /**
     * Связь с пользователем, создавшим объявление.
     *
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с категорией объявления.
     *
     * @return BelongsTo<Category, self>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Связь с фотографиями объявления.
     *
     * @return HasMany<Photo>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('is_main', 'desc');
    }

    /**
     * Связь с чатами, связанными с объявлением.
     *
     * @return HasMany<Chat>
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Связь с просмотрами объявления.
     *
     * @return HasMany<View>
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

    /**
     * Связь с избранным (пользователи, добавившие объявление).
     *
     * @return HasMany<Favorite>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Форматированная цена с пробелами и символом рубля.
     *
     * @return Attribute<string, never>
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->price, 0, '.', ' ') . ' руб.'
        );
    }

    /**
     * Скоуп для активных и одобренных объявлений.
     *
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeActiveAndApproved(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('approved', true);
    }

    /**
     * Проверяет, добавлено ли объявление в избранное текущим пользователем.
     *
     * @return bool
     */
    public function isFavoritedByCurrentUser(): bool
    {
        return auth()->check() && $this->favorites()->where('user_id', auth()->id())->exists();
    }
}