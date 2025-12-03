<?php

namespace Wave8\Factotum\Base\Console\Commands;

use DateTimeImmutable;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Wave8\Factotum\Base\Database\Seeder\DatabaseSeeder;

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
    protected $signature = 'factotum-base:install
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
            $this->publishConfigs();
            $this->publishLang();
            $this->publishMigrations();

            $this->publishModels();
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
            'create_personal_access_tokens_table.php',
            'create_notifications_table.php',
            'create_language_lines_table.php',
            'create_permission_tables.php',
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

        $this->callSilent('vendor:publish', ['--tag' => 'query-builder-config', '--force' => $force]);
        $this->callSilent('vendor:publish', ['--tag' => 'factotum-cms-config', '--force' => $force]);

        collect([
            'disable_invalid_filter_query_exception',
            'disable_invalid_sort_query_exception',
            'disable_invalid_includes_query_exception',
        ])->each(function (string $key) {
            if (config("query-builder.{$key}") === true) {
                return;
            }

            $this->files->replaceInFile("'{$key}' => false,", "'{$key}' => true,", config_path('query-builder.php'));

            throw_if(config("query-builder.{$key}") !== true, "Failed to update query-builder.{$key} value.");
        });

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

        // Vendor migrations are renamed after being published to ensure the
        // correct order due to differences in publishing methods.
        // i.e. vendor:publish command uses a $publishedAt Carbon instance and
        // adds 1 second for each migration, while make:notifications-table
        // uses the current time at the moment of execution, and spatie's
        // migrations are not registered as proper migrations.

        $this->callSilent('vendor:publish', ['--tag' => 'sanctum-migrations', '--force' => $force]);
        $this->callSilent('make:notifications-table');
        $this->callSilent('vendor:publish', ['--tag' => 'translation-loader-migrations', '--force' => $force]);
        $this->callSilent('vendor:publish', ['--tag' => 'permission-migrations', '--force' => $force]);

        Date::setTestNow(now());

        collect([
            'create_personal_access_tokens_table.php',
            'create_notifications_table.php',
            'create_language_lines_table.php',
            'create_permission_tables.php',
        ])->each(function (string $name) {
            Date::setTestNow(now()->addSecond());
            $this->renameMigrationFile($name);
        });

        $this->callSilent('vendor:publish', ['--tag' => 'factotum-base-migrations', '--force' => $force]);

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

    private function publishModels(): void
    {
        if (! $this->option('force') &&
            ! confirm('Would you like to publish the Factotum Base models to your application?')
        ) {
            warning(<<<'EOT'
Remember to extend the Factotum Base models in your application and customize them as needed.
EOT);

            return;
        }

        $this->files->copy(__DIR__.'/../../../stubs/app/Models/User.php', app_path('Models/User.php'));
    }

    private function publishProviders(): void
    {
        $force = $this->option('force');

        $this->callSilent('vendor:publish', ['--tag' => 'factotum-base-provider', '--force' => $force]);

        ServiceProvider::addProviderToBootstrapFile('App\Providers\FactotumBaseServiceProvider');

        $this->laravel->register('App\Providers\FactotumBaseServiceProvider');
    }

    private function runMigrations(): bool
    {
        if (! $this->option('migrate') &&
            ! confirm('New database migrations were added. Would you like to re-run your migrations?')
        ) {
            return false;
        }

        Artisan::call('migrate:fresh', ['--force' => true], $this->output);

        return true;
    }

    private function seedData(): bool
    {
        if (! $this->option('seed') &&
            ! confirm('Would you like to seed the database with initial data?')
        ) {
            return false;
        }

        Artisan::call('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true], $this->output);

        return true;
    }
}
