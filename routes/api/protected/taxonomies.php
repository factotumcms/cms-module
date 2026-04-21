<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\TaxonomyController;
use Wave8\Factotum\Cms\Models\Taxonomy;

Route::prefix('taxonomies')
    ->controller(TaxonomyController::class)

    ->group(function () {
        Route::get('{taxonomy}', 'read')->can('read', 'taxonomy');
        Route::post('', 'store')->can('create', Taxonomy::class);
        Route::put('{taxonomy}', 'update')->can('update', 'taxonomy');
        Route::delete('{taxonomy}', 'destroy')->can('delete', 'taxonomy');

        // Content type association
        Route::post('{taxonomy}/content-types/{contentType}', 'attachContentType')->can('update', 'taxonomy');
        Route::delete('{taxonomy}/content-types/{contentType}', 'detachContentType')->can('update', 'taxonomy');
    });
