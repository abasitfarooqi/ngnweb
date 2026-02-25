<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultNGNRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default category
        DB::table('ngn_categories')->updateOrInsert(
            ['id' => 1],
            ['name' => 'No-Category-Specified', 'description' => 'No category specified', 'image_url' => 'no_category.jpg']
        );

        // Default brand
        DB::table('ngn_brands')->updateOrInsert(
            ['id' => 1],
            ['name' => 'No-Brand-Specified', 'description' => 'No brand specified', 'image_url' => 'no_brand.jpg']
        );

        // Default model
        DB::table('ngn_models')->updateOrInsert(
            ['id' => 1],
            ['name' => 'No-Model-Specified', 'description' => 'No model specified']
        );
    }
}
