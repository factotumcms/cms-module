<?php

namespace Wave8\Factotum\Cms\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Wave8\Factotum\Cms\Jobs\GenerateSitemap;

#[AsCommand('factotum-cms:generate-sitemap')]
final class DispatchGenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factotum-cms:generate-sitemap
        {--sync : Run synchronously instead of dispatching to the queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap XML files for all published content';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! config('factotum_cms.sitemap.enabled', true)) {
            $this->components->warn('Sitemap generation is disabled.');

            return self::SUCCESS;
        }

        if ($this->option('sync')) {
            $this->components->info('Generating sitemap synchronously...');
            GenerateSitemap::dispatchSync();
        } else {
            $this->components->info('Dispatching sitemap generation job...');
            GenerateSitemap::dispatch();
        }

        $this->components->info('Sitemap generation completed successfully.');

        return self::SUCCESS;
    }
}
