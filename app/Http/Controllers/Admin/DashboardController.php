<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Post;
use App\Models\Region;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'Berita Terbit' => Post::published()->count(),
                'Draft' => Post::where('status', 'draft')->count(),
                'Total Dibaca' => (int) Post::sum('views'),
                'Iklan Aktif' => Ad::live()->count(),
                'Wilayah' => Region::count(),
                'Kategori' => Category::count(),
            ],
            'recent' => Post::with(['region', 'category'])->latest()->take(6)->get(),
            'popular' => Post::published()->popular()->take(6)->get(),
            'topAds' => Ad::orderByDesc('clicks')->take(5)->get(),
        ]);
    }
}
