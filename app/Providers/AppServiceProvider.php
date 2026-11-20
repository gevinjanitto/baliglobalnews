<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\LandingAd;
use App\Models\Page;
use App\Models\Post;
use App\Models\Region;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        Paginator::defaultView('partials.pagination');
        Schema::defaultStringLength(191);

        View::composer('layouts.site', function ($view) {
            $view->with([
                'menuCategories' => Category::where('is_active', true)->where('show_in_menu', true)->orderBy('sort_order')->get(),
                'menuRegions' => Region::whereNull('parent_id')->where('is_active', true)->where('show_in_menu', true)
                    ->with(['children' => fn ($q) => $q->where('is_active', true)])->orderBy('sort_order')->get(),
                'footerPages' => Page::where('show_in_footer', true)->orderBy('sort_order')->get(),
                'breaking' => Post::published()->where('is_breaking', true)->latest('published_at')->take(8)->get(['title', 'slug']),
                'headerAds' => LandingAd::forSlot('header'),
            ]);
        });
    }
}
