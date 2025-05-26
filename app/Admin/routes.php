<?php

use App\Admin\Controllers\Api\AttributeGroupController as AttributeGroupApiController;
use App\Admin\Controllers\Api\CommonController;
use App\Admin\Controllers\Api\FileController as FileApiController;
use App\Admin\Controllers\Api\PageBuilderAdminApiController;
use App\Admin\Controllers\ArticleCategoryController;
use App\Admin\Controllers\ArticleController;
use App\Admin\Controllers\AttributeController;
use App\Admin\Controllers\AttributeGroupController;
use App\Admin\Controllers\AttributeValueController;
use App\Admin\Controllers\FileController;
use App\Admin\Controllers\OrderController;
use App\Admin\Controllers\PageController;
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

    // Content Management Routes
    Route::resource('articles', ArticleController::class)->names('articles');
    Route::resource('article/categories', ArticleCategoryController::class)->names('article.categories');
    Route::resource('pages', PageController::class)->names('pages');
    Route::resource('files/manager', FileController::class)->names('files.manager');

    Route::get('pages/{pageId}/design', [PageController::class, 'design'])->name('pages.design');

    // Product Management Routes
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('product/categories', ProductCategoryController::class)->names('product.categories');
    Route::resource('product/skus', ProductSkuController::class)->names('product.skus');

    // Order Management Routes
    Route::resource('orders', OrderController::class)->names('orders');

    // Attribute Management Routes
    Route::resource('attribute/groups', AttributeGroupController::class)->names('attribute.groups');
    Route::resource('attributes', AttributeController::class)->names('attributes');
    Route::resource('attribute/values', AttributeValueController::class)->names('attribute.values');

    // API Routes
    Route::prefix('api/')->name('api.')->group(function () {
        Route::get('files/library', [FileApiController::class, 'library'])->name('files.library');
        Route::post('common/quick-edit', [CommonController::class, 'quickEdit'])->name('common.quick-edit');
        Route::get('attribute/groups', [AttributeGroupApiController::class, 'query'])->name('attribute.groups');

        // Page Builder Routes
        Route::get('pages/{pageId}/data', [PageBuilderAdminApiController::class, 'getPageData'])->name('api.pages.data');
        Route::post('pages/{pageId}/save', [PageBuilderAdminApiController::class, 'savePageData'])->name('api.pages.save');
        Route::get('pages/components/registry', [PageBuilderAdminApiController::class, 'getComponentsRegistry'])->name('api.components.registry');
    });
});
