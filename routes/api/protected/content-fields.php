<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentFieldController;

Route::prefix('content-types/{contentType}')->group(function () {
    Route::prefix('content-fields')
        ->controller(ContentFieldController::class)

        ->group(function () {
            Route::post('', 'store')->can('createContentField', 'contentType');
            Route::put('{contentField}', 'update')->can('update', 'contentField');
        });
});

Route::prefix('content-fields')
    ->controller(ContentFieldController::class)
    ->group(function () {});
