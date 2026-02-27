<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DerbiSeeder extends Seeder
{
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Derbi')->first()->id;

        $bikeModels = [
            ['model' => 'ATLANTIS'],
            ['model' => 'BOULEVARD'],
            ['model' => 'CROSS CITY'],
            ['model' => 'GP1 50'],
            ['model' => 'GP1 125'],
            ['model' => 'GP1 250'],
            ['model' => 'GP 50'],
            ['model' => 'GPR 50'],
            ['model' => 'GPR 125'],
            ['model' => 'MINIMOTO'],
            ['model' => 'MULHACEN 125'],
            ['model' => 'MULHACEN 650'],
            ['model' => 'RAMBLA'],
            ['model' => 'SENDA 50 HYP'],
            ['model' => 'SENDA 50 R'],
            ['model' => 'SENDA 50 SM'],
            ['model' => 'SENDA 125 R'],
            ['model' => 'SENDA 125 R-SM'],
            ['model' => 'SENDA 125 SM'],
            ['model' => 'SONAR 50'],
            ['model' => 'SONAR 125'],
            ['model' => 'SONAR 150'],
            ['model' => 'TERRA'],
            ['model' => 'VARIANT'],
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
