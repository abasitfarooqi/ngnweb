<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BSASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'BSA')->first()->id;

        $bikeModels = [
            ['model' => 'BSA Clothing'],
            ['model' => 'Gold Star'],
            ['model' => 'Gold Star Luggage'],
        ];

        if (DB::table('bike_models')->where('brand_name_id', $hondaId)->exists()) {
            return;
        }

        foreach ($bikeModels as $bikeModel) {
            DB::table('bike_models')->insert([
                'brand_name_id' => $hondaId,
                'model' => $bikeModel['model'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
