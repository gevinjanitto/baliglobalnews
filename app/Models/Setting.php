<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    public const DEFAULTS = [
        'site_name' => 'Bali Global News',
        'tagline' => 'Media Informasi Masyarakat',
        'motto' => 'Viral - Update - Terpercaya',
        'about' => 'Bali Global News adalah media informasi masyarakat yang menyajikan berita viral, update dan terpercaya dari seluruh wilayah Bali, nasional hingga internasional.',
        'facebook' => 'https://www.facebook.com/Bali-Global-News-509381966158651',
        'twitter' => 'https://twitter.com/baliglobalnews',
        'instagram' => 'https://instagram.com/baliglobalnews',
        'youtube' => '',
        'email' => 'info@baliglobalnews.com',
        'phone' => '',
        'address' => 'Jl. Akses Selatan Puspem Badung No.09, Banjar Umegunung, Kel. Sempidi, Kec. Mengwi, Badung, Bali',
        'ticker_enabled' => '1',
    ];

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    protected static ?array $cache = null;

    public static function get(string $key, $default = null)
    {
        if (static::$cache === null) {
            static::$cache = Schema::hasTable('settings') ? static::pluck('value', 'key')->all() : [];
        }

        return static::$cache[$key] ?? $default ?? (static::DEFAULTS[$key] ?? null);
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache = null;
    }
}
