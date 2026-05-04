<?php

namespace Wave8\Factotum\Cms\Console\Commands;

use DateTimeImmutable;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Wave8\Factotum\Cms\Database\Seeder\DatabaseSeeder as CmsDatabaseSeeder;

use function Illuminate\Filesystem\join_paths;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

#[AsCommand('factotum-cms:install')]
final class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factotum-cms:install
        {--migrate : Run database migrations (fresh)}
        {--seed : Seed the database with initial data}
        {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Factotum Cms module';

    public function __construct(
        private readonly Filesystem $files,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expectationsMet = collect([
            fn () => $this->ensureLocalEnvironment(),
            fn () => $this->ensureDateNotImmutable(),
            fn () => $this->ensureVendorMigrationsNotPublished(),
        ])->every(fn (callable $task) => $task());

        if (! $expectationsMet) {
            return self::FAILURE;
        }

        try {
            //Install Factotum base first
            Artisan::call('factotum-base:install');

            $this->publishConfigs();

            $this->publishMigrations();


            $this->publishProviders();

            collect([
                fn () => $this->runMigrations(),
                fn () => $this->seedData(),
            ])->every(fn (callable $task) => $task());

            info('Factotum Cms module installed successfully.');
        } catch (RuntimeException $exception) {
            warning($exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function ensureLocalEnvironment(): bool
    {
        if ($this->laravel->isLocal()) {
            return true;
        }

        $env = $this->laravel->environment();

        warning("Cannot run installation in the current environment ({$env}).");

        return false;
    }

    private function ensureDateNotImmutable(): bool
    {
        if (! ($now = now()) instanceof DateTimeImmutable) {
            return true;
        }

        $className = class_basename($now);

        warning(<<<"EOT"
Application is configured to use immutable dates ({$className}).
This may cause issues during the installation process.
Please temporarily switch to mutable dates and try again.
EOT
        );

        return false;
    }

    private function ensureVendorMigrationsNotPublished(): bool
    {
        $migrations = collect([
        ])->reject(fn (string $name) => $this->getMigrationPath($name) === null);

        if ($migrations->isEmpty()) {
            return true;
        }

        $list = $migrations->map(fn (string $migration) => "- {$migration}")->join("\n");

        warning(<<<"EOT"
Some of the vendor migrations are already published in your application.
Please remove them before proceeding to ensure the proper order.

{$list}
EOT);

        return false;
    }

    private function publishConfigs(): void
    {
        $force = $this->option('force');

        $this->callSilent('vendor:publish', ['--tag' => 'factotum-cms-config', '--force' => $force]);

        $this->components->info('Configuration files published successfully.');
    }

    private function publishLang(): void
    {
        $force = $this->option('force');

        $this->callSilent('lang:publish', ['--force' => $force]);

        $this->components->info('Language files published successfully.');
    }

    private function publishMigrations(): void
    {
        $force = $this->option('force');

        Date::setTestNow(now());

        $this->callSilent('vendor:publish', ['--tag' => 'factotum-cms-migrations', '--force' => $force]);

        Date::setTestNow();

        $this->components->info('Migration files published successfully.');
    }

    private function renameMigrationFile(string $name): void
    {
        $path = $this->getMigrationPath($name);

        throw_if($path === null, "Migration {$name} not found.");

        $datePrefix = now()->format('Y_m_d_His');
        $basename = $this->files->basename($path);

        if ($basename === "{$datePrefix}_{$name}") {
            return;
        }

        $this->files->move(
            $path,
            preg_replace('/\d{4}_\d{2}_\d{2}_\d{6}_/', "{$datePrefix}_", $path)
        );
    }

    private function getMigrationPath(string $name): ?string
    {
        return head($this->files->glob(join_paths(database_path('migrations'), "*_{$name}"))) ?: null;
    }

    private function publishProviders(): void
    {
        $force = $this->option('force');

        $this->callSilent('vendor:publish', ['--tag' => 'factotum-cms-provider', '--force' => $force]);

        ServiceProvider::addProviderToBootstrapFile('App\Providers\FactotumCmsServiceProvider');

        $this->laravel->register('App\Providers\FactotumCmsServiceProvider');
    }

    private function runMigrations(): bool
    {
        if (! $this->option('migrate') &&
            ! confirm('New database migrations were added. Would you like to re-run your migrations?')
        ) {
            return false;
        }

        Artisan::call('migrate', ['--force' => true], $this->output);

        return true;
    }

    private function seedData(): bool
    {
        if (! $this->option('seed') &&
            ! confirm('Would you like to seed the database with initial data?')
        ) {
            return false;
        }

        Artisan::call('db:seed', ['--class' => CmsDatabaseSeeder::class, '--force' => true], $this->output);

        return true;
    }
}
