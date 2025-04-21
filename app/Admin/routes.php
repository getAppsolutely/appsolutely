<?php

use App\Admin\Controllers\Api\FileController as FileApiController;
use App\Admin\Controllers\ArticleCategoryController;
use App\Admin\Controllers\ArticleController;
use App\Admin\Controllers\FileController;
use App\Admin\Controllers\ProductCategoryController;
use App\Admin\Controllers\ProductController;
use App\Admin\Controllers\ProductSkuController;
use Dcat\Admin\Admin;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function () {
    Route::get('/', 'HomeController@index');

    Route::resource('files/manager', FileController::class)->names('files.manager');
    Route::resource('article-categories', ArticleCategoryController::class)->names('article_categories');
    Route::resource('articles', ArticleController::class)->names('articles');
    Route::resource('product-categories', ProductCategoryController::class)->names('product_categories');
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('product-skus', ProductSkuController::class)->names('product_skus');

    Route::prefix('api/')->name('api.')->group(function () {
        Route::get('files/library', [FileApiController::class, 'library'])->name('files.library');
    });
});
