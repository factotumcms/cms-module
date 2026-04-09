<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\ContentFieldController;

Route::prefix('content-fields')
    ->controller(ContentFieldController::class)

    ->group(function () {});
