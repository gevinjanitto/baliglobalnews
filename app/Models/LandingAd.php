<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingAd extends Model
{
    public const SLOTS = [
        'header' => ['Di bawah Header', 'Tampil di bawah menu, semua halaman depan'],
        'home_top' => ['Setelah Berita Utama', 'Setelah blok Berita Utama'],
        'home_middle' => ['Tengah Landing', 'Di antara Berita Terbaru dan Wilayah'],
        'home_sidebar' => ['Sidebar Landing', 'Di samping daftar Terpopuler'],
        'home_bottom' => ['Bawah Landing', 'Sebelum footer'],
    ];

    public $timestamps = false;

    protected $fillable = ['slot', 'ad_id', 'sort_order'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public static function forSlot(string $slot)
    {
        return Ad::live()
            ->join('landing_ads', 'landing_ads.ad_id', '=', 'ads.id')
            ->where('landing_ads.slot', $slot)
            ->orderBy('landing_ads.sort_order')
            ->select('ads.*')
            ->get();
    }
}
