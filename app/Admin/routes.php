<?php

use App\Admin\Controllers\ArticleCategoryController;
use App\Admin\Controllers\ArticleController;
use App\Admin\Controllers\FileController;
use Dcat\Admin\Admin;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function () {
    Route::get('/', 'HomeController@index');

    Route::get('files/library', [FileController::class, 'library'])->name('files.library');
    Route::resource('files/manager', FileController::class)->names('files.manager');

    Route::resource('article-categories', ArticleCategoryController::class)->names('article_categories');
    Route::resource('articles', ArticleController::class)->names('articles');
});
