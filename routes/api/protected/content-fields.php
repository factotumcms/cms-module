<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentFieldController;
use Wave8\Factotum\Cms\Models\ContentType;

Route::prefix('content-fields')
    ->controller(ContentFieldController::class)

    ->group(function () {
        Route::post('', 'store')->can('create', ContentType::class);
    });
