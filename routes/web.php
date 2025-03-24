<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::prefix(LaravelLocalization::setLocale())->middleware(['localeCookieRedirect', 'localizationRedirect', 'localeViewPath'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

// Non-localization group
Route::middleware([])->group(function () {

    Route::get('uploads/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('file.retrieve');
    Route::get('assets/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('file.retrieve.assets');

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
