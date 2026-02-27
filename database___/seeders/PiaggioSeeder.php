<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PiaggioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Piaggio')->first()->id;

        $bikeModels =
            [
                ['model' => 'BEVERLY 125'],
                ['model' => 'BEVERLY 200'],
                ['model' => 'BEVERLY 250'],
                ['model' => 'BEVERLY 300'],
                ['model' => 'BEVERLY 350'],
                ['model' => 'BEVERLY 400'],
                ['model' => 'BEVERLY 500'],
                ['model' => 'CARNABY 125'],
                ['model' => 'CARNABY 200'],
                ['model' => 'CARNABY 250'],
                ['model' => 'CARNABY 300'],
                ['model' => 'CIAO'],
                ['model' => 'DIESIS 50'],
                ['model' => 'DIESIS 100'],
                ['model' => 'FLY 50'],
                ['model' => 'FLY 100'],
                ['model' => 'FLY 125 ALL'],
                ['model' => 'FLY 150'],
                ['model' => 'FREE'],
                ['model' => 'HEXAGON'],
                ['model' => 'LIBERTY 50'],
                ['model' => 'LIBERTY 125'],
                ['model' => 'LIBERTY 125 ALL'],
                ['model' => 'LIBERTY 150'],
                ['model' => 'LIBERTY 200'],
                ['model' => 'MEDLEY 125'],
                ['model' => 'MEDLEY 150'],
                ['model' => 'MP3 125'],
                ['model' => 'MP3 250'],
                ['model' => 'MP3 300'],
                ['model' => 'MP3 400'],
                ['model' => 'MP3 500'],
                ['model' => 'NRG'],
                ['model' => 'NTT'],
                ['model' => 'SFERA'],
                ['model' => 'SI MIX'],
                ['model' => 'SKIPPER'],
                ['model' => 'SKR CITY'],
                ['model' => 'SUPER EXAGON'],
                ['model' => 'SUPER HEXAGON'],
                ['model' => 'TYPHOON 50'],
                ['model' => 'TYPHOON 125'],
                ['model' => 'VELOFAX'],
                ['model' => 'VESPA'],
                ['model' => 'WI-BIKE'],
                ['model' => 'X7 125'],
                ['model' => 'X7 250'],
                ['model' => 'X7 300'],
                ['model' => 'X8 125'],
                ['model' => 'X8 150'],
                ['model' => 'X8 200'],
                ['model' => 'X8 250'],
                ['model' => 'X8 400'],
                ['model' => 'X9'],
                ['model' => 'X9 125'],
                ['model' => 'X9 180'],
                ['model' => 'X9 200'],
                ['model' => 'X9 250'],
                ['model' => 'X9 500'],
                ['model' => 'X10 125'],
                ['model' => 'X10 350'],
                ['model' => 'X10 500'],
                ['model' => 'X EVO 125'],
                ['model' => 'X EVO 250'],
                ['model' => 'X EVO 400'],
                ['model' => 'ZIP'],
                ['model' => 'ZIP 50'],
                ['model' => 'ZIP 95'],
                ['model' => 'ZIP 100'],
                ['model' => 'ZIP 125'],
                ['model' => 'ZIP SP'],
                ['model' => 'ZIP SP 50'],
                ['model' => 'ZIP SP H2O'],
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
