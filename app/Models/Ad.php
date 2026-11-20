<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    public const FORMATS = ['banner' => 'Banner Lebar (mis. 1550x500 / 970x250)', 'kotak' => 'Kotak / Sidebar (mis. 300x250 / 300x600)'];

    protected $fillable = ['name', 'image', 'link_url', 'format', 'is_active', 'starts_at', 'ends_at'];

    protected $casts = ['is_active' => 'boolean', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public function posts()
    {
        return $this->belongsToMany(Post::class)->withPivot('placement');
    }

    public function scopeLive($q)
    {
        return $q->where('is_active', true)
            ->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($w) => $w->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function getImageUrlAttribute(): ?string
    {
        return media_url($this->image);
    }

    public function getIsLiveAttribute(): bool
    {
        return $this->is_active
            && (! $this->starts_at || $this->starts_at->lte(now()))
            && (! $this->ends_at || $this->ends_at->gte(now()));
    }
}
