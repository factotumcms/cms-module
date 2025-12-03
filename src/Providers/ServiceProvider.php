<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // API Services
        $this->app->singleton(
            ContentTypeServiceInterface::class, ContentTypeService::class
        );
    }
}
