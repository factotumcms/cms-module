<?php

namespace Wave8\Factotum\Cms\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wave8\Factotum\Cms\Contracts\SitemapGeneratorServiceInterface;
use Wave8\Factotum\Cms\Enums\ContentStatus;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\UrlAlias;

class SitemapGeneratorService implements SitemapGeneratorServiceInterface
{
    private string $baseUrl;

    private int $maxUrlsPerFile;

    private string $outputPath;

    private string $defaultChangefreq;

    private string $defaultPriority;

    private array $routableTypes;

    private bool $pingGoogle;

    private bool $pingBing;

    public function __construct()
    {
        $config = config('factotum_cms.sitemap');

        $this->baseUrl = rtrim($config['base_url'] ?? config('app.url', 'http://localhost'), '/');
        $this->maxUrlsPerFile = $config['max_urls_per_file'] ?? 50000;
        $this->outputPath = $config['output_path'] ?? public_path();
        $this->defaultChangefreq = $config['default_changefreq'] ?? 'weekly';
        $this->defaultPriority = $config['default_priority'] ?? '0.5';
        $this->routableTypes = $config['routable_types'] ?? [];
        $this->pingGoogle = $config['ping_google'] ?? true;
        $this->pingBing = $config['ping_bing'] ?? true;
    }

    public function generate(): array
    {
        $this->cleanOldSitemaps();

        $generatedFiles = [];

        // Get all canonical URL aliases with their routable models
        $urlAliases = UrlAlias::canonical()
            ->with('routable')
            ->orderBy('locale')
            ->orderBy('uri')
            ->get();

        // Filter out unpublished content and soft-deleted models
        $urlAliases = $urlAliases->filter(function (UrlAlias $alias) {
            $routable = $alias->routable;

            if (! $routable) {
                return false;
            }

            // Check soft deletes
            if (method_exists($routable, 'trashed') && $routable->trashed()) {
                return false;
            }

            // Check content status
            if ($routable instanceof Content && $routable->status !== ContentStatus::PUBLISHED) {
                return false;
            }

            return true;
        });

        // Group by locale for sub-sitemaps
        $grouped = $urlAliases->groupBy(fn (UrlAlias $alias) => $alias->locale?->value ?? 'default');

        // Build hreflang mapping: routable_type+routable_id => [locale => uri]
        $hreflangMap = $this->buildHreflangMap($urlAliases);

        $fileIndex = 0;
        foreach ($grouped as $locale => $aliases) {
            $chunks = $aliases->chunk($this->maxUrlsPerFile);

            foreach ($chunks as $chunkIndex => $chunk) {
                $fileIndex++;
                $filename = "sitemap-{$locale}-".($chunkIndex + 1).'.xml';
                $filePath = $this->outputPath.'/'.$filename;

                $xml = $this->generateSubSitemap($chunk, $hreflangMap);
                File::put($filePath, $xml);

                $generatedFiles[] = $filename;
            }
        }

        // Generate sitemap index
        $indexPath = $this->outputPath.'/sitemap.xml';
        $indexXml = $this->generateSitemapIndex($generatedFiles);
        File::put($indexPath, $indexXml);

        array_unshift($generatedFiles, 'sitemap.xml');

        // Ping search engines
        $this->pingSearchEngines();

        Log::info('Sitemap generated successfully.', [
            'files' => $generatedFiles,
            'total_urls' => $urlAliases->count(),
        ]);

        return $generatedFiles;
    }

    public function pingSearchEngines(): void
    {
        $sitemapUrl = $this->baseUrl.'/sitemap.xml';

        if ($this->pingGoogle) {
            $this->ping("https://www.google.com/ping?sitemap={$sitemapUrl}");
        }

        if ($this->pingBing) {
            $this->ping("https://www.bing.com/ping?sitemap={$sitemapUrl}");
        }
    }

    private function generateSubSitemap($aliases, array $hreflangMap): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.PHP_EOL;
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">'.PHP_EOL;

        foreach ($aliases as $alias) {
            $xml .= $this->generateUrlEntry($alias, $hreflangMap);
        }

        $xml .= '</urlset>'.PHP_EOL;

        return $xml;
    }

    private function generateUrlEntry(UrlAlias $alias, array $hreflangMap): string
    {
        $routable = $alias->routable;
        $routableType = get_class($routable);

        $changefreq = $this->routableTypes[$routableType]['changefreq'] ?? $this->defaultChangefreq;
        $priority = $this->routableTypes[$routableType]['priority'] ?? $this->defaultPriority;

        $loc = $this->baseUrl.'/'.ltrim($alias->uri, '/');
        $lastmod = $routable->updated_at instanceof Carbon
            ? $routable->updated_at->toW3cString()
            : Carbon::parse($routable->updated_at)->toW3cString();

        $xml = '  <url>'.PHP_EOL;
        $xml .= "    <loc>{$this->escapeXml($loc)}</loc>".PHP_EOL;
        $xml .= "    <lastmod>{$lastmod}</lastmod>".PHP_EOL;
        $xml .= "    <changefreq>{$changefreq}</changefreq>".PHP_EOL;
        $xml .= "    <priority>{$priority}</priority>".PHP_EOL;

        // Add hreflang alternate links
        $key = $routableType.':'.$alias->routable_id;
        if (isset($hreflangMap[$key]) && count($hreflangMap[$key]) > 1) {
            foreach ($hreflangMap[$key] as $hreflangLocale => $hreflangUri) {
                $hreflangLoc = $this->baseUrl.'/'.ltrim($hreflangUri, '/');
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$hreflangLocale}\" href=\"{$this->escapeXml($hreflangLoc)}\" />".PHP_EOL;
            }
        }

        $xml .= '  </url>'.PHP_EOL;

        return $xml;
    }

    private function generateSitemapIndex(array $filenames): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach ($filenames as $filename) {
            $loc = $this->baseUrl.'/'.$filename;
            $lastmod = Carbon::now()->toW3cString();

            $xml .= '  <sitemap>'.PHP_EOL;
            $xml .= "    <loc>{$this->escapeXml($loc)}</loc>".PHP_EOL;
            $xml .= "    <lastmod>{$lastmod}</lastmod>".PHP_EOL;
            $xml .= '  </sitemap>'.PHP_EOL;
        }

        $xml .= '</sitemapindex>'.PHP_EOL;

        return $xml;
    }

    /**
     * Build a map of routable_type:routable_id => [locale => canonical_uri]
     * by using the translation groups to find all locale variants.
     */
    private function buildHreflangMap($urlAliases): array
    {
        $map = [];

        // Group aliases by their translation group
        $translationGroups = [];

        foreach ($urlAliases as $alias) {
            $routable = $alias->routable;

            if (! $routable || ! method_exists($routable, 'translationGroup')) {
                // No translation support, just add its own locale
                $key = get_class($routable).':'.$alias->routable_id;
                $locale = $alias->locale?->value ?? 'it';
                $map[$key][$locale] = $alias->uri;

                continue;
            }

            $group = $routable->translationGroup();

            if ($group) {
                $translationGroups[$group][] = $alias;
            } else {
                $key = get_class($routable).':'.$alias->routable_id;
                $locale = $alias->locale?->value ?? 'it';
                $map[$key][$locale] = $alias->uri;
            }
        }

        // For each translation group, map all locales to each member
        foreach ($translationGroups as $group => $aliases) {
            $localeUriMap = [];
            foreach ($aliases as $alias) {
                $locale = $alias->locale?->value ?? 'it';
                $localeUriMap[$locale] = $alias->uri;
            }

            // Apply the full locale map to each member of the group
            foreach ($aliases as $alias) {
                $key = get_class($alias->routable).':'.$alias->routable_id;
                $map[$key] = $localeUriMap;
            }
        }

        return $map;
    }

    private function cleanOldSitemaps(): void
    {
        $files = File::glob($this->outputPath.'/sitemap*.xml');

        foreach ($files as $file) {
            File::delete($file);
        }
    }

    private function ping(string $url): void
    {
        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                Log::info("Sitemap ping successful: {$url}");
            } else {
                Log::warning("Sitemap ping failed: {$url}", [
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Sitemap ping error: {$url}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
