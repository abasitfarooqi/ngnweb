<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GileraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Triumph')->first()->id;

        $bikeModels = [
            ['model' => 'COGUAR'],
            ['model' => 'DNA 50'],
            ['model' => 'DNA 125'],
            ['model' => 'DNA 180'],
            ['model' => 'DNA GP'],
            ['model' => 'DNA M.Y.'],
            ['model' => 'EASY MOVING'],
            ['model' => 'FUOCO 500'],
            ['model' => 'GP 800'],
            ['model' => 'GPR 50'],
            ['model' => 'GSM'],
            ['model' => 'GSM M.Y.'],
            ['model' => 'H@K'],
            ['model' => 'H@K M.Y.'],
            ['model' => 'ICE'],
            ['model' => 'MOTORBIKE RCR SMT SC'],
            ['model' => 'NEXUS 125'],
            ['model' => 'NEXUS 250'],
            ['model' => 'NEXUS 300'],
            ['model' => 'NEXUS 500'],
            ['model' => 'RCR'],
            ['model' => 'RUNNER'],
            ['model' => 'RUNNER 50'],
            ['model' => 'RUNNER 125'],
            ['model' => 'RUNNER 180'],
            ['model' => 'RUNNER 180 CAT.'],
            ['model' => 'RUNNER 200'],
            ['model' => 'RUNNER VX'],
            ['model' => 'SMT'],
            ['model' => 'STALKER'],
            ['model' => 'STORM'],
            ['model' => 'STORM 50'],
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
