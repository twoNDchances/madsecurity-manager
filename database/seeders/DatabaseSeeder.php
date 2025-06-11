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
        $this->call([
            TagSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            PolicySeeder::class,
            TargetSeeder::class,
        ]);
    }
}
