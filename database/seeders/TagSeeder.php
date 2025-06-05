<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::firstOrCreate(
            ['name' => 'default assets'],
            [
                'color'=> '#a855f7',
                'description' => 'Created by default',
            ],
        );
    }
}
