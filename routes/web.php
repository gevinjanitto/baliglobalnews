<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('post.show');
Route::get('/kategori/{slug}', [NewsController::class, 'category'])->name('category.show');
Route::get('/tag/{slug}', [NewsController::class, 'tag'])->name('tag.show');
Route::get('/wilayah/{slug}', [NewsController::class, 'region'])->name('region.show');
Route::get('/indeks', [NewsController::class, 'latest'])->name('latest');
Route::get('/cari', [NewsController::class, 'search'])->name('search');
Route::get('/halaman/{slug}', [NewsController::class, 'page'])->name('page.show');
Route::get('/iklan/{ad}/klik', [NewsController::class, 'adClick'])->name('ad.click');
Route::get('/media/{path}', [NewsController::class, 'media'])->where('path', '.*')->name('media');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics');
        Route::post('posts/preview', [Admin\PostController::class, 'preview'])->name('posts.preview');
        Route::post('posts/upload-image', [Admin\PostController::class, 'uploadImage'])->name('posts.upload-image');
        Route::resource('posts', Admin\PostController::class)->except('show');
        Route::resource('regions', Admin\RegionController::class)->except('show');
        Route::resource('categories', Admin\CategoryController::class)->except('show');
        Route::resource('ads', Admin\AdController::class)->except('show');
        Route::resource('pages', Admin\PageController::class)->except('show');
        Route::get('landing-ads', [Admin\LandingAdController::class, 'edit'])->name('landing-ads.edit');
        Route::put('landing-ads', [Admin\LandingAdController::class, 'update'])->name('landing-ads.update');
        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::put('settings/password', [Admin\SettingController::class, 'password'])->name('settings.password');
    });
});
