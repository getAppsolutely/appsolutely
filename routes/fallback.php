<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix(LaravelLocalization::setLocale())->middleware(['localeCookieRedirect', 'localizationRedirect', 'localeViewPath'])->group(function () {
    Route::get('/{slug?}', [PageController::class, 'show'])->name('pages.show');
});
