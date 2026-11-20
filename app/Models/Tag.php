<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public static function idsFrom(?string $csv): array
    {
        $names = collect(explode(',', (string) $csv))->map(fn ($n) => trim(preg_replace('/\s+/', ' ', $n)))
            ->filter(fn ($n) => $n !== '' && Str::slug($n) !== '')->unique(fn ($n) => Str::slug($n))->take(30);

        return $names->map(fn ($n) => static::firstOrCreate(['slug' => Str::slug($n)], ['name' => Str::limit($n, 60, '')])->id)->all();
    }
}
