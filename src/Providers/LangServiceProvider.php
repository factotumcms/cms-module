<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class LangServiceProvider extends LaravelServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__.'/../../lang' => base_path('lang'),
        ], ['factotum-cms-lang']);
    }
}
