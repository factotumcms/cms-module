<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ConfigServiceProvider extends LaravelServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('factotum_cms.php'),
        ], ['factotum-cms-config']);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            'factotum_cms'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../../config/data_transfer.php',
            'data_transfer'
        );
    }
}
