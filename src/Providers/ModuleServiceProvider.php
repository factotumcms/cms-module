<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Wave8\Factotum\Cms\Console\Commands\Install;


class ModuleServiceProvider extends LaravelServiceProvider
{
    public function register(): void
    {
        // Register DI services
        $this->app->register(ServiceProvider::class);
        //
        // Register app required service providers
        $this->app->register(ConfigServiceProvider::class);
        $this->app->register(LangServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        // Register commands
        $this->registerCommands();
    }

    public function boot(): void
    {
        $this->configurePublishing();
    }

    public function registerCommands(): void
    {
        $this->commands([
            Install::class,
        ]);
    }

    private function configurePublishing(): void
    {
        $this->publishesMigrations([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'factotum-cms-migrations');

        $this->publishes([
            __DIR__ . '/../../stubs/app/Providers/FactotumCmsServiceProvider.php' => app_path('Providers/FactotumCmsServiceProvider.php'),
        ], 'factotum-cms-provider');

        $this->loadTranslationsFrom(__DIR__ . '/../../lang');
    }
}
