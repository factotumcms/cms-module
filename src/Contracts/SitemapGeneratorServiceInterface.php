<?php

namespace Wave8\Factotum\Cms\Contracts;

interface SitemapGeneratorServiceInterface
{
    /**
     * Generate all sitemap files (sub-sitemaps + sitemap index).
     *
     * @return array<string> List of generated file paths
     */
    public function generate(): array;

    /**
     * Ping search engines to notify them of the updated sitemap.
     */
    public function pingSearchEngines(): void;
}
