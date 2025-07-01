<?php

use App\Admin\Controllers\Api\AttributeGroupController as AttributeGroupApiController;
use App\Admin\Controllers\Api\CommonController;
use App\Admin\Controllers\Api\FileController as FileApiController;
use App\Admin\Controllers\Api\PageBuilderAdminApiController;
use App\Admin\Controllers\ArticleCategoryController;
use App\Admin\Controllers\ArticleController;
use App\Admin\Controllers\FileController;
use App\Admin\Controllers\HomeController;
use App\Admin\Controllers\MenuController;
use App\Admin\Controllers\OrderController;
use App\Admin\Controllers\PageBlockController;
use App\Admin\Controllers\PageBlockEntryController;
use App\Admin\Controllers\PageBlockGroupController;
use App\Admin\Controllers\PageBlockSettingController;
use App\Admin\Controllers\PageController;
use App\Admin\Controllers\ProductAttributeController;
use App\Admin\Controllers\ProductAttributeEntry;
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
    Route::get('', [HomeController::class, 'index']);

    Route::resource('file-manager', FileController::class)->names('file-manager');
    Route::get('uploads/{path?}', [FileController::class, 'retrieve'])->where('path', '(.*)')->name('file.self');

    // Content Management Routes
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::resource('entry', ArticleController::class);
        Route::resource('categories', ArticleCategoryController::class)->names('categories');
    });

    Route::prefix('pages')->name('pages.')->group(function () {
        Route::resource('entry', PageController::class);
        Route::get('{reference}/design', [PageController::class, 'design'])->name('design');
        Route::resource('blocks/entry', PageBlockEntryController::class)->names('blocks.entry');
        Route::resource('block-settings', PageBlockSettingController::class)->names('block-settings');
        Route::resource('blocks', PageBlockController::class)->names('blocks');
        Route::resource('block-groups', PageBlockGroupController::class)->names('block-groups');
    });

    // Menu Management Routes
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::resource('entry', MenuController::class)->names('entry');
    });

    // Product Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::resource('entry', ProductController::class);
        Route::resource('categories', ProductCategoryController::class)->names('categories');
        Route::resource('skus', ProductSkuController::class)->names('skus');
        Route::resource('attributes/entry', ProductAttributeEntry::class)->names('attributes.entry');
        Route::resource('attribute-groups', ProductAttributeGroupController::class)->names('attribute-groups');
        Route::resource('attributes', ProductAttributeController::class)->names('attributes');
        Route::resource('attribute-values', ProductAttributeValueController::class)->names('attribute-values');
    });

    // Order Management Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::resource('entry', OrderController::class);
    });

    // Application releases
    Route::prefix('releases')->name('releases.')->group(function () {
        Route::resource('builds', ReleaseBuildController::class)->names('builds');
        Route::resource('versions', ReleaseVersionController::class)->names('versions');
    });

    // API Routes
    Route::prefix('api/')->name('api.')->group(function () {
        Route::get('file-library', [FileApiController::class, 'library'])->name('file-library');
        Route::post('common/quick-edit', [CommonController::class, 'quickEdit'])->name('common.quick-edit');
        Route::get('products/attribute-groups', [AttributeGroupApiController::class, 'query'])->name('attribute-groups');

        // Page Builder Routes
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('{reference}/data', [PageBuilderAdminApiController::class, 'getPageData'])->name('data');
            Route::put('{reference}/save', [PageBuilderAdminApiController::class, 'savePageData'])->name('save');
            Route::put('{reference}/reset', [PageBuilderAdminApiController::class, 'resetPageData'])->name('reset');
            Route::get('block-registry', [PageBuilderAdminApiController::class, 'getBlockRegistry'])->name('block-registry');
            Route::get('block/schema-fields', [PageBuilderAdminApiController::class, 'getSchemaFields'])->name('block.schema-fields');
        });
    });
});
