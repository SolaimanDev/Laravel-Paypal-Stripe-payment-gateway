<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\CurrencyTableSeeder;
use Database\Seeders\PaymentPlatformsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            PaymentPlatformsTableSeeder::class,
            CurrencyTableSeeder::class,
            PlanSeeder::class,
        ]);
    }
}
