<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScarabeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Scarabeo')->first()->id;

        $bikeModels = [
            ['model' => 'SCARABEO 50 2T'],
            ['model' => 'SCARABEO 50 4T'],
            ['model' => 'SCARABEO 50 DITECH'],
            ['model' => 'SCARABEO 100 2T'],
            ['model' => 'SCARABEO 100 4T'],
            ['model' => 'SCARABEO 125-150-200-250'],
            ['model' => 'SCARABEO 125-200 LIGHT'],
            ['model' => 'SCARABEO 250-300 LIGHT'],
            ['model' => 'SCARABEO 400-500'],
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
