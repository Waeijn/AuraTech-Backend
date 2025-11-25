<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This runs the separate files we created earlier
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
