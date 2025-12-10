<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ContentTypeSeeder::class,
            ContentFieldSeeder::class,
            ContentSeeder::class,
            LanguageSeeder::class
        ]);
    }
}
