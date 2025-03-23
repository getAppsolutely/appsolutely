<?php

use App\Admin\Controllers\FileController;
use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');

    $router->any('files', [FileController::class, 'upload'])->name('files.upload');
    $router->get('assets/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('file.retrieve');

    // Standard resource routes for files
    $router->resource('files/manager', FileController::class);

});
