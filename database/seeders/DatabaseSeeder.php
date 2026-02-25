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
        // $this->call(StepOneNgnStoreSeeder::class);
        // $this->call(NgnProductsLatestSeeder::class);
        // $this->call(NgnPOSProductsLatestSeeder::class);
        $this->call(ClubMemberSeeder::class);
    }
}
