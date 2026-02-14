<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'is_published',
        'published_at',
        'views_count',
        'reading_time',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'tags' => 'array',
            'views_count' => 'integer',
            'reading_time' => 'integer',
        ];
    }

    /**
     * Категория статьи
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Автор статьи
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Опубликованные статьи
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * По тегу
     */
    public function scopeWithTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Популярные
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('views_count');
    }

    /**
     * Последние
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('published_at');
    }

    /**
     * Увеличить счётчик просмотров
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Вычислить время чтения из body
     */
    public static function calculateReadingTime(string $body): int
    {
        $wordCount = str_word_count(strip_tags($body));
        $minutes = max(1, (int) ceil($wordCount / 200));
        return $minutes;
    }

    /**
     * Генерация slug из title
     */
    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = static::where('slug', $slug)->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Похожие статьи (та же категория)
     */
    public function relatedPosts(int $limit = 3)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Краткий текст для excerpt
     */
    public function getExcerptOrTruncatedBody(int $length = 160): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        return Str::limit(strip_tags($this->body), $length);
    }
}
