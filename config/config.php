<?php

use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\Term;

return [
    'module_name' => 'Cms',

    /*
    |--------------------------------------------------------------------------
    | Sitemap Configuration
    |--------------------------------------------------------------------------
    */
    'sitemap' => [
        // Enable or disable sitemap generation
        'enabled' => env('FACTOTUM_SITEMAP_ENABLED', true),

        // Base URL for sitemap links (defaults to APP_URL)
        'base_url' => env('FACTOTUM_SITEMAP_BASE_URL', env('APP_URL', 'http://localhost')),

        // Maximum number of URLs per sitemap file (XML standard limit: 50000)
        'max_urls_per_file' => 50000,

        // Output path relative to public directory
        'output_path' => public_path(),

        // Default values for sitemap entries
        'default_changefreq' => 'weekly',
        'default_priority' => '0.5',

        // Per-routable-type configuration overrides
        'routable_types' => [
            Content::class => [
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            Term::class => [
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
        ],

        // Search engine ping after generation
        'ping_google' => env('FACTOTUM_SITEMAP_PING_GOOGLE', true),
        'ping_bing' => env('FACTOTUM_SITEMAP_PING_BING', true),
    ],
];
