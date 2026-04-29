<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Models\UrlAlias;

interface UrlAliasServiceInterface
{
    /**
     * Resolve a URI to a UrlAlias.
     */
    public function resolve(string $uri): ?UrlAlias;

    /**
     * Create a URL alias for a routable entity.
     */
    public function createForRoutable(Model $routable, string $uri, Locale|string $locale, bool $isCanonical = true): UrlAlias;

    /**
     * Update the canonical URL for a routable, setting redirect from old URI.
     */
    public function updateCanonical(Model $routable, string $newUri, Locale|string $locale): UrlAlias;

    /**
     * Remove all URL aliases for a routable entity.
     */
    public function deleteForRoutable(Model $routable): void;

    /**
     * Get all URL aliases for a routable entity.
     */
    public function getForRoutable(Model $routable): Collection;

    /**
     * Set a redirect on an existing alias.
     */
    public function setRedirect(UrlAlias $alias, string $targetUri): UrlAlias;

    /**
     * Delete a single URL alias.
     */
    public function delete(UrlAlias $alias): bool;
}
