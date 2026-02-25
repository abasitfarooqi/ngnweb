<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotoGuzziSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Moto Guzzi')->first()->id;

        $bikeModels = [
            ['model' => '65 GT'],
            ['model' => '750 X'],
            ['model' => '850 T3'],
            ['model' => '850 T5'],
            ['model' => '1100 SPORT'],
            ['model' => '1200 SPORT'],
            ['model' => 'AUDACE'],
            ['model' => 'BELLAGIO'],
            ['model' => 'BREVA'],
            ['model' => 'CALIFORNIA'],
            ['model' => 'DAYTONA'],
            ['model' => 'ELDORADO'],
            ['model' => 'GRISO'],
            ['model' => 'GT 1000'],
            ['model' => 'LE MANS'],
            ['model' => 'MGS-01'],
            ['model' => 'MGX 21'],
            ['model' => 'NEVADA'],
            ['model' => 'NORGE'],
            ['model' => 'NTX'],
            ['model' => 'QUOTA'],
            ['model' => 'S'],
            ['model' => 'SP'],
            ['model' => 'STELVIO'],
            ['model' => 'STRADA'],
            ['model' => 'TARGA'],
            ['model' => 'V 7'],
            ['model' => 'V 9'],
            ['model' => 'V 10'],
            ['model' => 'V 11'],
            ['model' => 'V 35'],
            ['model' => 'V 50'],
            ['model' => 'V 65'],
            ['model' => 'V 75'],
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
