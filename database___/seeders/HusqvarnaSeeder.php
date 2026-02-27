<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HusqvarnaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Husqvarna')->first()->id;

        $bikeModels = [
            ['model' => '701 ENDURO'],
            ['model' => '701 SUPERMOTO'],
            ['model' => 'FC 250'],
            ['model' => 'FC 250 HQV'],
            ['model' => 'FC 350'],
            ['model' => 'FC 350 HQV'],
            ['model' => 'FC 450'],
            ['model' => 'FC 450 HQV'],
            ['model' => 'FE 250'],
            ['model' => 'FE 250 HQV'],
            ['model' => 'FE 350'],
            ['model' => 'FE 350 HQV'],
            ['model' => 'FE 350S'],
            ['model' => 'FE 450'],
            ['model' => 'FE 450 HQV'],
            ['model' => 'FE 501'],
            ['model' => 'FE 501 HQV'],
            ['model' => 'FE 501S'],
            ['model' => 'FR 450 RALLY'],
            ['model' => 'FS 450'],
            ['model' => 'FX 350'],
            ['model' => 'FX 450'],
            ['model' => 'TC 125'],
            ['model' => 'TC 125 HQV'],
            ['model' => 'TC 250'],
            ['model' => 'TC 250 HQV'],
            ['model' => 'TE 125'],
            ['model' => 'TE 125 HQV'],
            ['model' => 'TE 150'],
            ['model' => 'TE 250'],
            ['model' => 'TE 250 HQV'],
            ['model' => 'TE 300'],
            ['model' => 'TE 300 HQV'],
            ['model' => 'TX 125'],
            ['model' => 'TX 300'],
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
