<?php

use App\Admin\Controllers\Api\AttributeGroupController as AttributeGroupApiController;
use App\Admin\Controllers\Api\CommonController;
use App\Admin\Controllers\Api\DynamicFormApiController;
use App\Admin\Controllers\Api\FileController as FileApiController;
use App\Admin\Controllers\Api\NotificationApiController;
use App\Admin\Controllers\Api\PageBuilderAdminApiController;
use App\Admin\Controllers\ArticleCategoryController;
use App\Admin\Controllers\ArticleController;
use App\Admin\Controllers\DynamicFormController;
use App\Admin\Controllers\FileController;
use App\Admin\Controllers\HomeController;
use App\Admin\Controllers\MenuController;
use App\Admin\Controllers\NotificationController;
use App\Admin\Controllers\OrderController;
use App\Admin\Controllers\PageBlockController;
use App\Admin\Controllers\PageController;
use App\Admin\Controllers\ProductAttributeController;
use App\Admin\Controllers\ProductCategoryController;
use App\Admin\Controllers\ProductController;
use App\Admin\Controllers\ProductSkuController;
use App\Admin\Controllers\ReleaseController;
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
        Route::resource('blocks', PageBlockController::class)->names('blocks');
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
        Route::resource('attributes', ProductAttributeController::class)->names('attributes');
    });

    // Order Management Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::resource('entry', OrderController::class);
    });

    // Application releases
    Route::prefix('releases')->name('releases.')->group(function () {
        Route::resource('', ReleaseController::class)->names('entry');
    });

    // Dynamic Forms Management
    Route::prefix('forms')->name('forms.')->group(function () {
        Route::resource('', DynamicFormController::class)->only(['index'])->names('entry');
    });

    // Notifications Management
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::resource('', NotificationController::class)->only(['index'])->names('entry');
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

        // Dynamic Forms API Routes
        Route::prefix('forms')->name('forms.')->group(function () {
            Route::post('entries/{id}/mark-spam', [DynamicFormApiController::class, 'markAsSpam'])->name('entries.mark-spam');
            Route::post('entries/{id}/mark-not-spam', [DynamicFormApiController::class, 'markAsNotSpam'])->name('entries.mark-not-spam');
            Route::get('entries/export', [DynamicFormApiController::class, 'exportCsv'])->name('entries.export');
            Route::get('{formId}/entries/export', [DynamicFormApiController::class, 'exportCsv'])->name('entries.export-by-form');
        });

        // Notifications API Routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::post('process-queue', [NotificationApiController::class, 'processQueue'])->name('process-queue');
            Route::post('retry-failed', [NotificationApiController::class, 'retryFailed'])->name('retry-failed');
            Route::delete('clean-old', [NotificationApiController::class, 'cleanOld'])->name('clean-old');
            Route::post('{id}/retry', [NotificationApiController::class, 'retry'])->name('retry');
            Route::post('{id}/cancel', [NotificationApiController::class, 'cancel'])->name('cancel');
            Route::post('{id}/duplicate-template', [NotificationApiController::class, 'duplicateTemplate'])->name('duplicate-template');
            Route::post('test-rule/{id}', [NotificationApiController::class, 'testRule'])->name('test-rule');
            Route::get('{id}/preview', [NotificationApiController::class, 'preview'])->name('preview');
        });
    });
});
