<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    public const PLACEMENTS = [
        'atas' => 'Atas Artikel (di bawah foto)',
        'tengah' => 'Tengah Artikel (di sela paragraf)',
        'bawah' => 'Bawah Artikel',
        'sidebar' => 'Sidebar Artikel',
    ];

    protected $fillable = [
        'region_id', 'category_id', 'title', 'slug', 'excerpt', 'body', 'image', 'image_caption',
        'author_name', 'status', 'is_headline', 'is_editor_choice', 'is_breaking', 'published_at', 'views', 'views_manual',
    ];

    protected $casts = [
        'is_headline' => 'boolean', 'is_editor_choice' => 'boolean', 'is_breaking' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->orderBy('name');
    }

    public function scopeInCategory($q, int $categoryId)
    {
        return $q->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId));
    }

    public function scopePopular($q)
    {
        return $q->orderByRaw('COALESCE(views_manual, views) DESC');
    }

    public function getShownViewsAttribute(): int
    {
        return (int) ($this->views_manual ?? $this->views);
    }

    public function ads()
    {
        return $this->belongsToMany(Ad::class)->withPivot('placement');
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getImageUrlAttribute(): ?string
    {
        return media_url($this->image);
    }

    public function getUrlAttribute(): string
    {
        return route('post.show', $this->slug);
    }

    public function getSummaryAttribute(): string
    {
        return $this->excerpt ?: Str::limit(trim(strip_tags($this->body ?? '')), 180);
    }

    public function getReadMinutesAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body ?? '')) / 200));
    }

    public function getBadgeAttribute(): ?string
    {
        return $this->region?->name ?? $this->category?->name;
    }

    public function adsFor(string $placement)
    {
        return $this->ads->filter(fn ($a) => $a->pivot->placement === $placement && $a->is_live)->values();
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'berita';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
