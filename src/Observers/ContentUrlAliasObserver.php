<?php

namespace Wave8\Factotum\Cms\Observers;

use Wave8\Factotum\Cms\Contracts\Api\UrlAliasServiceInterface;
use Wave8\Factotum\Cms\Models\Content;

class ContentUrlAliasObserver
{
    public function __construct(
        private readonly UrlAliasServiceInterface $urlAliasService,
    ) {}

    public function created(Content $content): void
    {
        $this->generateUrlAliases($content);
    }

    public function updated(Content $content): void
    {
        // Only regenerate aliases if URL-relevant fields changed
        if ($content->wasChanged(['url', 'abs_url', 'lang'])) {
            $this->updateUrlAliases($content);
        }
    }

    public function deleted(Content $content): void
    {
        $this->urlAliasService->deleteForRoutable($content);
    }

    /**
     * Generate canonical URL alias for a newly created content.
     */
    private function generateUrlAliases(Content $content): void
    {
        $locale = $content->lang?->value ?? $content->getAttributes()['lang'];
        $uri = $this->buildContentUri($content);

        $this->urlAliasService->createForRoutable(
            routable: $content,
            uri: $uri,
            locale: $locale,
            isCanonical: true,
        );
    }

    /**
     * Update canonical URL alias when content URL changes.
     */
    private function updateUrlAliases(Content $content): void
    {
        $locale = $content->lang?->value ?? $content->getAttributes()['lang'];
        $uri = $this->buildContentUri($content);

        $this->urlAliasService->updateCanonical(
            routable: $content,
            newUri: $uri,
            locale: $locale,
        );
    }

    /**
     * Build the URI for a content from its abs_url.
     */
    private function buildContentUri(Content $content): string
    {
        return '/'.trim($content->abs_url, '/');
    }
}
