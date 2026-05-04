<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Wave8\Factotum\Cms\Console\Commands\DispatchGenerateSitemap;
use Wave8\Factotum\Cms\Console\Commands\Install;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Term;
use Wave8\Factotum\Cms\Observers\ContentFieldObserver;
use Wave8\Factotum\Cms\Observers\ContentTypeObserver;
use Wave8\Factotum\Cms\Observers\ContentUrlAliasObserver;
use Wave8\Factotum\Cms\Observers\TermUrlAliasObserver;

class ModuleServiceProvider extends LaravelServiceProvider
{
    public function register(): void
    {
        // Register DI services
        $this->app->register(ServiceProvider::class);

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
        $this->configureObservers();
        $this->configureScheduling();
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'factotum_cms');
    }

    public function registerCommands(): void
    {
        $this->commands([
            Install::class,
            DispatchGenerateSitemap::class,
        ]);
    }

    private function configurePublishing(): void
    {
        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'factotum-cms-migrations');

        $this->publishes([
            __DIR__.'/../../stubs/app/Providers/FactotumCmsServiceProvider.php' => app_path('Providers/FactotumCmsServiceProvider.php'),
        ], 'factotum-cms-provider');

        $this->loadTranslationsFrom(__DIR__.'/../../lang');
    }

    private function configureObservers(): void
    {
        ContentType::observe(ContentTypeObserver::class);
        ContentField::observe(ContentFieldObserver::class);
        Content::observe(ContentUrlAliasObserver::class);
        Term::observe(TermUrlAliasObserver::class);
    }

    private function configureScheduling(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            if (config('factotum_cms.sitemap.enabled', true)) {
                $schedule->command('factotum-cms:generate-sitemap --sync')->daily();
            }
        });
    }
}
