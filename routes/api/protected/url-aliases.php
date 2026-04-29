<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\UrlAliasController;

Route::prefix('url-aliases')
    ->controller(UrlAliasController::class)
    ->group(function () {
        Route::post('', 'store');
        Route::get('{urlAlias}', 'read')->can('read', 'urlAlias');
        Route::put('{urlAlias}', 'update')->can('update', 'urlAlias');
        Route::delete('{urlAlias}', 'destroy')->can('delete', 'urlAlias');

        // Get all aliases for a specific routable entity
        Route::get('for/{type}/{id}', 'forRoutable');
    });

