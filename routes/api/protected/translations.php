<?php

use Illuminate\Support\Facades\Route;
use Wave8\Factotum\Cms\Http\Controllers\Api\TranslationController;
use Wave8\Factotum\Cms\Models\Translation;

Route::prefix('translations')
    ->controller(TranslationController::class)

    ->group(function () {
        Route::post('link', 'link')->can('link', Translation::class);
        Route::delete('{translation}', 'unlink')->can('unlink', 'translation');
        Route::get('{type}/{id}', 'index')->can('read', Translation::class);
        Route::get('{type}/{id}/locales', 'locales')->can('read', Translation::class);
    });
