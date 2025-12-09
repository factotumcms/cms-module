<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentController;
use Wave8\Factotum\Cms\Models\ContentType;

Route::prefix('contents')
    ->controller(ContentController::class)

    ->group(function () {
        Route::post('', 'store')->can('create', ContentType::class);
    });
