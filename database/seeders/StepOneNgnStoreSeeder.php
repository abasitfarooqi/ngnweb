<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StepOneNgnStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Records Seeder
        $this->defaultRecords();

        // Brands Seeder
        $this->seedBrands();

        // Categories Seeder
        $this->seedCategories();
    }

    /**
     * Seed default category, brand, and model.
     */
    private function defaultRecords()
    {
        // Default category
        DB::table('ngn_categories')->updateOrInsert(
            ['name' => 'No-Category-Specified', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );

        // Default brand
        DB::table('ngn_brands')->updateOrInsert(
            ['name' => 'No-Brand-Specified', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );

        // Default model
        DB::table('ngn_models')->updateOrInsert(
            ['name' => 'No-Model-Specified', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
    }

    /**
     * Seed brand records.
     */
    private function seedBrands()
    {
        DB::table('ngn_brands')->insert([
            ['name' => 'OXFORD', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ALPINESTARS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'MINT', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'BOX', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'HJC', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'MT', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SPARTAN', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ARMR', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SLIME', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'REMA', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SIMPSON', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CYCLE GLOVES', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'GRYPP', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ACF50', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'MUC OFF', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'TYREART', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'TRACKER', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'DOJO', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ROK STRAPS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ALCOSENSE', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'GT85', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SKIN SOLUTIONS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'NOVA', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'BULL-IT', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }

    /**
     * Seed category records.
     */
    private function seedCategories()
    {
        DB::table('ngn_categories')->insert([
            ['name' => 'ACCESSORIES', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CARE', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CASUAL', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CLOTHING', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'FOOTWEAR', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'GLOVES', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'HEADWEAR', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'HELMET', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'HOTGRIPS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'INTERCOMS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'JACKET', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'JEANS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'LAYERS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'LEGGINGS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'LIGHTING', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'LOCKS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'LUGGAGE', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'MINT', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'PANTS', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'RAINWEAR', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SPARES', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SUIT', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'TRANSPORT', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'WORKSHOP', 'image_url' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
