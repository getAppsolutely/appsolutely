<?php

use App\Admin\Controllers\FileController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');

    $router->post('file/upload', [FileController::class, 'upload']);

    // Standard resource routes for files
    $router->resource('files', FileController::class);


});
