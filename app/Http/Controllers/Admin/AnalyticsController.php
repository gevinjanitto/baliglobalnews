<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public const RANGES = ['today' => 'Hari ini', '7' => '7 hari', '30' => '30 hari', '90' => '90 hari'];

    public function index(Request $request)
    {
        Visit::resolveGeo();

        $range = array_key_exists($request->query('range'), self::RANGES) ? $request->query('range') : '7';
        $from = $range === 'today' ? now()->startOfDay() : now()->subDays((int) $range - 1)->startOfDay();
        $base = fn () => Visit::where('created_at', '>=', $from);
        $count = fn (string $col, int $n = 8) => $base()->select($col.' as label', DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT visitor_id) as visitors'))
            ->whereNotNull($col)->groupBy($col)->orderByDesc('total')->take($n)->get();

        $daily = $base()->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_id) as visitors'))
            ->groupBy('d')->orderBy('d')->get()->keyBy('d');
        $days = collect();
        for ($d = $from->copy(); $d->lte(now()); $d->addDay()) {
            $k = $d->toDateString();
            $days->push(['label' => $d->locale('id')->translatedFormat('d M'), 'views' => (int) ($daily[$k]->views ?? 0), 'visitors' => (int) ($daily[$k]->visitors ?? 0)]);
        }

        $topPosts = $base()->whereNotNull('post_id')->select('post_id', DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT visitor_id) as visitors'))
            ->groupBy('post_id')->orderByDesc('total')->take(15)->get();
        $postMap = Post::whereIn('id', $topPosts->pluck('post_id'))->get()->keyBy('id');

        $visitors = $base()->select('visitor_id', DB::raw('MAX(created_at) as last_seen'), DB::raw('MIN(created_at) as first_seen'), DB::raw('COUNT(*) as pages'), DB::raw('MAX(id) as last_id'))
            ->groupBy('visitor_id')->orderByDesc('last_seen')->paginate(20, ['*'], 'vpage')->withQueryString();
        $lastVisits = Visit::with('post')->whereIn('id', $visitors->pluck('last_id'))->get()->keyBy('id');

        return view('admin.analytics', [
            'range' => $range,
            'summary' => [
                'Tayangan Halaman' => $base()->count(),
                'Pengunjung Unik' => $base()->distinct()->count('visitor_id'),
                'Berita Dibaca' => $base()->whereNotNull('post_id')->count(),
                'Online (5 menit)' => Visit::where('created_at', '>=', now()->subMinutes(5))->distinct()->count('visitor_id'),
            ],
            'days' => $days,
            'topPosts' => $topPosts->map(fn ($r) => ['post' => $postMap->get($r->post_id), 'total' => $r->total, 'visitors' => $r->visitors])->filter(fn ($r) => $r['post']),
            'countries' => $count('country'),
            'cities' => $count('city'),
            'devices' => $count('device', 4),
            'browsers' => $count('browser'),
            'oses' => $count('os'),
            'referrers' => $count('referrer'),
            'visitors' => $visitors,
            'lastVisits' => $lastVisits,
            'recent' => Visit::with('post')->latest('id')->take(30)->get(),
        ]);
    }
}
