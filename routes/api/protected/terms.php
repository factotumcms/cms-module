<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\TermController;

Route::prefix('taxonomies/{taxonomy}')->group(function () {
    Route::prefix('terms')
        ->controller(TermController::class)

        ->group(function () {
            Route::get('tree', 'tree');
            Route::post('', 'store')->can('createTerm', 'taxonomy');
            Route::get('{term}', 'read')->can('read', 'term');
            Route::put('{term}', 'update')->can('update', 'term');
            Route::delete('{term}', 'destroy')->can('delete', 'term');
        });
});

// Content term association routes
Route::prefix('content-types/{contentType}')->group(function () {
    Route::prefix('contents/{content}/terms')
        ->controller(TermController::class)

        ->group(function () {
            Route::get('', 'contentTerms')->can('read', 'content');
            Route::post('sync', 'syncToContent')->can('update', 'content');
        });
});
