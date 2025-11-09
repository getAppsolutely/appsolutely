<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ReleaseController;
use Illuminate\Support\Facades\Route;

/**
 * Release API routes
 */
Route::get('releases/latest', [ReleaseController::class, 'latest'])->name('api.releases.latest');
