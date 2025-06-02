<?php

use App\Http\Controllers\Api\ReleaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('releases/latest', [ReleaseController::class, 'latest']);
