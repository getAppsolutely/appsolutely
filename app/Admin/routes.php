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

    $router->get('assets/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('file.retrieve');
    $router->post('file/upload', [FileController::class, 'upload']);

    // Standard resource routes for files
    $router->resource('files', FileController::class);

});
