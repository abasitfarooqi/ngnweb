<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KTMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'KTM')->first()->id;

        $bikeModels = [
            ['model' => 'ATV/QUAD 450cc'],
            ['model' => 'ATV/QUAD 505cc'],
            ['model' => 'ATV/QUAD 525cc'],
            ['model' => 'ELECTRIC'],
            ['model' => 'OFF ROAD 50cc'],
            ['model' => 'OFF ROAD 60cc'],
            ['model' => 'OFF ROAD 65cc'],
            ['model' => 'OFF ROAD 85cc'],
            ['model' => 'OFF ROAD 105cc'],
            ['model' => 'OFF ROAD 125cc'],
            ['model' => 'OFF ROAD 150cc'],
            ['model' => 'OFF ROAD 200cc'],
            ['model' => 'OFF ROAD 250cc'],
            ['model' => 'OFF ROAD 300cc'],
            ['model' => 'OFF ROAD 350cc'],
            ['model' => 'OFF ROAD 380cc'],
            ['model' => 'OFF ROAD 400cc'],
            ['model' => 'OFF ROAD 450cc'],
            ['model' => 'OFF ROAD 500cc'],
            ['model' => 'OFF ROAD 505cc'],
            ['model' => 'OFF ROAD 520cc'],
            ['model' => 'OFF ROAD 525cc'],
            ['model' => 'OFF ROAD 530cc'],
            ['model' => 'OFF ROAD 540cc'],
            ['model' => 'OFF ROAD 620cc'],
            ['model' => 'OFF ROAD 660cc'],
            ['model' => 'OFF ROAD 690cc'],
            ['model' => 'RACE 250cc'],
            ['model' => 'ROAD 50cc'],
            ['model' => 'ROAD 125cc'],
            ['model' => 'ROAD 200cc'],
            ['model' => 'ROAD 250cc'],
            ['model' => 'ROAD 390cc'],
            ['model' => 'ROAD 400cc'],
            ['model' => 'ROAD 450cc'],
            ['model' => 'ROAD 525cc'],
            ['model' => 'ROAD 560cc'],
            ['model' => 'ROAD 620cc'],
            ['model' => 'ROAD 625cc'],
            ['model' => 'ROAD 640cc'],
            ['model' => 'ROAD 660cc'],
            ['model' => 'ROAD 690cc'],
            ['model' => 'ROAD 790cc'],
            ['model' => 'ROAD 890cc'],
            ['model' => 'ROAD 950cc'],
            ['model' => 'ROAD 990cc'],
            ['model' => 'ROAD 1050cc'],
            ['model' => 'ROAD 1090cc'],
            ['model' => 'ROAD 1190cc'],
            ['model' => 'ROAD 1290cc'],
            ['model' => 'ROAD RC 125cc'],
            ['model' => 'ROAD RC 200cc'],
            ['model' => 'ROAD RC 390cc'],
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
