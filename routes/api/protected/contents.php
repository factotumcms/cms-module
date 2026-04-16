<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentController;

Route::prefix('content-types/{contentType}')->group(function () {
    Route::prefix('contents')
        ->controller(ContentController::class)

        ->group(function () {
            Route::post('', 'store')->can('createContent', 'contentType');
            //            Route::put('{contentField}', 'update')->can('update', 'contentField');
            //            Route::get('{contentField}', 'read')->can('read', 'contentField');
            //            Route::delete('{contentField}', 'destroy')->can('delete', 'contentField');
        });
});
