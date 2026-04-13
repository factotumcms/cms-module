<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentTypeController;
use Wave8\Factotum\Cms\Models\ContentType;

Route::prefix('content-types')
    ->controller(ContentTypeController::class)

    ->group(function () {
        Route::get('{contentType}', 'read')->can('read', 'contentType');
        Route::post('', 'store')->can('create', ContentType::class);
        Route::put('{contentType}', 'update')->can('update', 'contentType');
        Route::delete('{contentType}', 'destroy')->can('delete', 'contentType');
    });
