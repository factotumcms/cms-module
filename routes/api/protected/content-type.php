<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentTypeController;
use Wave8\Factotum\Cms\Models\ContentType;

Route::prefix('content-type')
    ->controller(ContentTypeController::class)

    ->group(function () {
        Route::post('', 'store')->can('create', ContentType::class);
    });
