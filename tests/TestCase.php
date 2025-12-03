<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Wave8\Factotum\Base\Database\Seeder\DatabaseSeeder;

#[WithMigration('laravel', 'cache', 'queue')]
#[WithMigration('notifications')]
abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    use WithWorkbench;

    protected $enablesPackageDiscoveries = true;

    protected function defineDatabaseMigrations(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'translation-loader-migrations']);
        $this->artisan('vendor:publish', ['--tag' => 'permission-migrations']);
    }

    protected function defineDatabaseSeeders(): void
    {
        $this->seed(DatabaseSeeder::class);
    }
}
