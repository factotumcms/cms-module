<?php

namespace Wave8\Factotum\Cms\Observers;

use Wave8\Factotum\Cms\Contracts\Api\UrlAliasServiceInterface;
use Wave8\Factotum\Cms\Models\Term;

class TermUrlAliasObserver
{
    public function __construct(
        private readonly UrlAliasServiceInterface $urlAliasService,
    ) {}

    public function created(Term $term): void
    {
        $this->generateUrlAlias($term);
    }

    public function updated(Term $term): void
    {
        if ($term->wasChanged(['slug', 'parent_id', 'lang'])) {
            $this->updateUrlAlias($term);
            $this->updateDescendantAliases($term);
        }
    }

    public function deleted(Term $term): void
    {
        $this->urlAliasService->deleteForRoutable($term);
    }

    /**
     * Generate canonical URL alias for a newly created term.
     */
    private function generateUrlAlias(Term $term): void
    {
        $locale = $term->lang?->value ?? $term->getAttributes()['lang'];
        $uri = $this->buildTermUri($term);

        $this->urlAliasService->createForRoutable(
            routable: $term,
            uri: $uri,
            locale: $locale,
            isCanonical: true,
        );
    }

    /**
     * Update canonical URL alias when term slug or parent changes.
     */
    private function updateUrlAlias(Term $term): void
    {
        $locale = $term->lang?->value ?? $term->getAttributes()['lang'];
        $uri = $this->buildTermUri($term);

        $this->urlAliasService->updateCanonical(
            routable: $term,
            newUri: $uri,
            locale: $locale,
        );
    }

    /**
     * When a parent term changes slug/position, update all descendant aliases.
     */
    private function updateDescendantAliases(Term $term): void
    {
        $descendants = $term->descendants()->defaultOrder()->get();

        foreach ($descendants as $descendant) {
            $locale = $descendant->lang?->value ?? $descendant->getAttributes()['lang'];
            $uri = $this->buildTermUri($descendant);

            $this->urlAliasService->updateCanonical(
                routable: $descendant,
                newUri: $uri,
                locale: $locale,
            );
        }
    }

    /**
     * Build the hierarchical URI for a term.
     * Pattern: /{taxonomy-slug}/{ancestor-slug}/...//{term-slug}
     */
    private function buildTermUri(Term $term): string
    {
        $term->loadMissing('taxonomy');

        $taxonomySlug = $term->taxonomy->name;
        $hierarchicalPath = $term->buildHierarchicalPath();

        return '/'.trim($taxonomySlug.'/'.$hierarchicalPath, '/');
    }
}

