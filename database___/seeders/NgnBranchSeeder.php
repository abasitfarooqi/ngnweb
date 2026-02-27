<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ngn_branch')->insert([
            [
                'name' => 'Tooting - Neguinho Motors Ltd',
                'location' => '4a Penwortham Road London SW16 6RE',
            ],
            [
                'name' => 'Catford - Neguinho Motors Ltd',
                'location' => '9-13 Unit 1179 Catford Hill London SE4 4NU',
            ],
        ]);
    }
}
