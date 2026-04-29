<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Wave8\Factotum\Cms\Contracts\Api\ContentFieldServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TaxonomyServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TermServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TranslationServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\UrlAliasServiceInterface;
use Wave8\Factotum\Cms\Services\Api\ContentFieldService;
use Wave8\Factotum\Cms\Services\Api\ContentService;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;
use Wave8\Factotum\Cms\Services\Api\TaxonomyService;
use Wave8\Factotum\Cms\Services\Api\TermService;
use Wave8\Factotum\Cms\Services\Api\TranslationService;
use Wave8\Factotum\Cms\Services\Api\UrlAliasService;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // API Services
        $this->app->singleton(ContentTypeServiceInterface::class, ContentTypeService::class);
        $this->app->singleton(ContentFieldServiceInterface::class, ContentFieldService::class);
        $this->app->singleton(ContentServiceInterface::class, ContentService::class);
        $this->app->singleton(TaxonomyServiceInterface::class, TaxonomyService::class);
        $this->app->singleton(TermServiceInterface::class, TermService::class);
        $this->app->singleton(TranslationServiceInterface::class, TranslationService::class);
        $this->app->singleton(UrlAliasServiceInterface::class, UrlAliasService::class);
    }
}
