<?php

use App\Admin\Controllers\Api\AttributeGroupController as AttributeGroupApiController;
use App\Admin\Controllers\Api\CommonController;
use App\Admin\Controllers\Api\FileController as FileApiController;
use App\Admin\Controllers\Api\PageBuilderAdminApiController;
use App\Admin\Controllers\ArticleCategoryController;
use App\Admin\Controllers\ArticleController;
use App\Admin\Controllers\FileController;
use App\Admin\Controllers\HomeController;
use App\Admin\Controllers\OrderController;
use App\Admin\Controllers\PageBlockController;
use App\Admin\Controllers\PageBlockGroupController;
use App\Admin\Controllers\PageBlockSettingController;
use App\Admin\Controllers\PageController;
use App\Admin\Controllers\ProductAttributeController;
use App\Admin\Controllers\ProductAttributeGroupController;
use App\Admin\Controllers\ProductAttributeValueController;
use App\Admin\Controllers\ProductCategoryController;
use App\Admin\Controllers\ProductController;
use App\Admin\Controllers\ProductSkuController;
use App\Admin\Controllers\ReleaseBuildController;
use App\Admin\Controllers\ReleaseVersionController;
use Dcat\Admin\Admin;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function () {
    Route::get('/', [HomeController::class, 'index']);

    Route::get('uploads/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('file.self');

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

    // Attribute Management Routes
    Route::resource('product/attribute/groups', ProductAttributeGroupController::class)->names('attribute.groups');
    Route::resource('product/attributes', ProductAttributeController::class)->names('attributes');
    Route::resource('product/attribute/values', ProductAttributeValueController::class)->names('attribute.values');

    // Order Management Routes
    Route::resource('orders', OrderController::class)->names('orders');

    // Application release
    Route::resource('release/builds', ReleaseBuildController::class)->names('app.builds');
    Route::resource('release/versions', ReleaseVersionController::class)->names('app.versions');

    // Block Management Routes
    Route::resource('page/block/groups', PageBlockGroupController::class)->names('page.block.groups');
    Route::resource('page/blocks', PageBlockController::class)->names('page.blocks');
    Route::resource('page/block/settings', PageBlockSettingController::class)->names('page.block.settings');

    // API Routes
    Route::prefix('api/')->name('api.')->group(function () {
        Route::get('files/library', [FileApiController::class, 'library'])->name('files.library');
        Route::post('common/quick-edit', [CommonController::class, 'quickEdit'])->name('common.quick-edit');
        Route::get('attribute/groups', [AttributeGroupApiController::class, 'query'])->name('attribute.groups');

        // Page Builder Routes
        Route::get('pages/{pageId}/data', [PageBuilderAdminApiController::class, 'getPageData'])->name('pages.data');
        Route::put('pages/{pageId}/save', [PageBuilderAdminApiController::class, 'savePageData'])->name('pages.save');
        Route::get('pages/blocks/registry', [PageBuilderAdminApiController::class, 'getBlocksRegistry'])->name('blocks.registry');
    });

});
