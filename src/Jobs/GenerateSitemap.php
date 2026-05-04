<?php

namespace Wave8\Factotum\Cms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wave8\Factotum\Cms\Contracts\SitemapGeneratorServiceInterface;

class GenerateSitemap implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    /**
     * Execute the job.
     */
    public function handle(SitemapGeneratorServiceInterface $service): void
    {
        if (! config('factotum_cms.sitemap.enabled', true)) {
            return;
        }

        $service->generate();
    }
}
