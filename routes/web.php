<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Localization group
Route::prefix(LaravelLocalization::setLocale())->middleware(['localeCookieRedirect', 'localizationRedirect', 'localeViewPath'])->group(function () {
    // Route::get('/{slug?}', [PageController::class, 'show'])->name('pages.show');
});

// Non-localization group
Route::middleware([])->group(function () {
    Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
    Route::get('sitemap-{type}.xml', [SitemapController::class, 'type'])
        ->where('type', 'page|article|product')
        ->name('sitemap.type');
    Route::get('assets/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('file.public.assets');
    Route::get('storage/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('book');

});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

require __DIR__ . '/auth.php';
