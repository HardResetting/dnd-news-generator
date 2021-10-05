<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Templates::factory(10)->create();
        \App\Models\Type::factory(10)->create();
        \App\Models\Item::factory(10)->create();
    }
}
