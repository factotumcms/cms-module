<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentController;

Route::prefix('content-types/{contentType}')->group(function () {
    Route::prefix('contents')
        ->controller(ContentController::class)

        ->group(function () {
            Route::post('', 'store')->can('createContent', 'contentType');
            Route::put('{content}', 'update')->can('update', 'content');
            Route::get('{content}', 'read')->can('read', 'content');
            Route::delete('{content}', 'destroy')->can('delete', 'content');
        });
});
