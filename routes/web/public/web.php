<?php

// Catch-all resolve endpoint — must be registered last
use Wave8\Factotum\Cms\Http\Controllers\Web\FrontController;

Route::get('{path}', FrontController::class)->where('path', '.*');
