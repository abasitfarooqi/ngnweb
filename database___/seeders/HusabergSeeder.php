<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HusabergSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Husaberg')->first()->id;

        $bikeModels = [
            ['model' => 'FE 350'],
            ['model' => 'FE 250'],
            ['model' => 'FE 390'],
            ['model' => 'FE 450'],
            ['model' => 'FE 450 s'],
            ['model' => 'FE 450e/6'],
            ['model' => 'FE 501'],
            ['model' => 'FE 550 s'],
            ['model' => 'FE 550e/6'],
            ['model' => 'FE 570'],
            ['model' => 'FE 570 S'],
            ['model' => 'FE 650e/6'],
            ['model' => 'FS 450 s'],
            ['model' => 'FS 450c/6'],
            ['model' => 'FS 450e/6'],
            ['model' => 'FS 550e/6'],
            ['model' => 'FS 570'],
            ['model' => 'FS 650c/6'],
            ['model' => 'FS 650e/6'],
            ['model' => 'FX 450'],
            ['model' => 'TC 50'],
            ['model' => 'TC 65'],
            ['model' => 'TC 85 17/14'],
            ['model' => 'TC 85 17/14 HQV'],
            ['model' => 'TC 85 19/16'],
            ['model' => 'TC 85 19/16 HQV'],
            ['model' => 'TE 125'],
            ['model' => 'TE 250'],
            ['model' => 'TE 300'],
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
