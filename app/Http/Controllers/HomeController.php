<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LandingAd;
use App\Models\Post;
use App\Models\Region;

class HomeController extends Controller
{
    public function index()
    {
        $base = fn () => Post::published()->with(['region', 'category'])->latest('published_at');

        $top = $base()->where('is_headline', true)->take(8)->get();
        if ($top->count() < 8) {
            $top = $top->concat($base()->whereNotIn('id', $top->pluck('id'))->take(8 - $top->count())->get());
        }
        $editor = $base()->where('is_editor_choice', true)->take(9)->get();
        $latest = $base()->take(5)->get();
        $popular = Post::published()->with(['region', 'category'])->popular()->take(6)->get();

        $bali = Region::where('slug', 'bali')->first();
        $baliChildren = $bali ? $bali->children()->where('is_active', true)->get() : collect();
        $regionTabs = $baliChildren->map(function ($r) use ($base) {
            return ['region' => $r, 'posts' => $base()->whereIn('region_id', $r->descendantIds())->take(6)->get()];
        })->filter(fn ($t) => $t['posts']->isNotEmpty())->values();
        $baliPosts = $bali ? $base()->whereIn('region_id', $bali->descendantIds())->take(9)->get() : collect();

        $homeCategories = Category::where('is_active', true)->where('show_on_home', true)->orderBy('sort_order')->get()
            ->map(fn ($c) => ['category' => $c, 'posts' => $base()->inCategory($c->id)->take(3)->get()])
            ->filter(fn ($s) => $s['posts']->isNotEmpty())->values();

        $popularCategories = Category::where('is_active', true)->withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')->take(8)->get();

        $ads = collect(array_keys(LandingAd::SLOTS))->mapWithKeys(fn ($s) => [$s => LandingAd::forSlot($s)]);

        return view('site.home', compact(
            'top', 'editor', 'latest', 'popular', 'regionTabs', 'baliPosts', 'bali',
            'homeCategories', 'popularCategories', 'ads'
        ));
    }
}
