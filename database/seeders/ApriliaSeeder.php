<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApriliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Aprilia')->first()->id;

        $bikeModels = [
            ['model' => 'AF1 FUTURA 50'],
            ['model' => 'AF1 FUTURA 125'],
            ['model' => 'AREA 51'],
            ['model' => 'ATLANTIC'],
            ['model' => 'CAPONORD 1000'],
            ['model' => 'CAPONORD 1200'],
            ['model' => 'DORSODURO 750'],
            ['model' => 'DORSODURO 1200'],
            ['model' => 'GULLIVER'],
            ['model' => 'LEONARDO 125-150'],
            ['model' => 'LEONARDO 250-300'],
            ['model' => 'LEONARDO ST'],
            ['model' => 'MINI RX'],
            ['model' => 'MOJITO (HABANA) 125-150'],
            ['model' => 'MOJITO 50 CUSTOM'],
            ['model' => 'MOJITO 50 RETRO'],
            ['model' => 'MOTO'],
            ['model' => 'MX 50'],
            ['model' => 'MX 125'],
            ['model' => 'MXV 450'],
            ['model' => 'NA MANA'],
            ['model' => 'PEGASO 50'],
            ['model' => 'PEGASO 125'],
            ['model' => 'PEGASO 600'],
            ['model' => 'PEGASO 650'],
            ['model' => 'RALLY 50'],
            ['model' => 'RS4 50 (2T)'],
            ['model' => 'RS 50 (2T)'],
            ['model' => 'RS 125 (2T)'],
            ['model' => 'RS 125 (4T)'],
            ['model' => 'RS 125 (122CC)'],
            ['model' => 'RS 125 (123CC)'],
            ['model' => 'RS 250'],
            ['model' => 'RST FUTURA'],
            ['model' => 'RSV 2 1000 (2 CYL)'],
            ['model' => 'RSV 4 1000 (V4 Models)'],
            ['model' => 'RSV 4 1000 FACTORY'],
            ['model' => 'RSV 4 1000 FACTORY (V4 models)'],
            ['model' => 'RX-SX 50'],
            ['model' => 'RX-SX 125'],
            ['model' => 'RXV-SXV 450-550'],
            ['model' => 'SHIVER 750'],
            ['model' => 'SHIVER 750 GT'],
            ['model' => 'SL FALCO 1000'],
            ['model' => 'SONIC'],
            ['model' => 'SPORT CITY 125-200-250'],
            ['model' => 'SPORT CITY CUBE 125-200'],
            ['model' => 'SPORT CITY CUBE 250-300'],
            ['model' => 'SPORT CITY ONE 50 (2T)'],
            ['model' => 'SPORT CITY ONE 50 (4T)'],
            ['model' => 'SPORT CITY ONE 125'],
            ['model' => 'SPORT CITY STREET 125'],
            ['model' => 'SPORT CITY STREET 300'],
            ['model' => 'SR 50 (AIR COOLED)'],
            ['model' => 'SR 50 (WATER COOLED)'],
            ['model' => 'SR 125-150'],
            ['model' => 'SR 300'],
            ['model' => 'SRV 850'],
            ['model' => 'TUAREG RALLY 50'],
            ['model' => 'TUONO 50'],
            ['model' => 'TUONO 125'],
            ['model' => 'TUONO 1000'],
            ['model' => 'TUONO 1100 FACTORY'],
            ['model' => 'TUONO 1100 FACTORY (V4 Models)'],
            ['model' => 'TUONO 1100 RR'],
            ['model' => 'TUONO 1100 RR (V4 Models)'],
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
