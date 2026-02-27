<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VespaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Vespa')->first()->id;

        $bikeModels = [
            ['model' => 'GRANTURISMO 125'],
            ['model' => 'GRANTURISMO 200'],
            ['model' => 'VESPA 50'],
            ['model' => 'VESPA 946'],
            ['model' => 'VESPA ET2 50'],
            ['model' => 'VESPA ET4 50'],
            ['model' => 'VESPA ET4 125'],
            ['model' => 'VESPA ET4 150'],
            ['model' => 'VESPA GT 125'],
            ['model' => 'VESPA GT 200'],
            ['model' => 'VESPA GT 250'],
            ['model' => 'VESPA GTS 125'],
            ['model' => 'VESPA GTS 150'],
            ['model' => 'VESPA GTS 250'],
            ['model' => 'VESPA GTS 300'],
            ['model' => 'VESPA GTV 250'],
            ['model' => 'VESPA GTV 300'],
            ['model' => 'VESPA LX 50'],
            ['model' => 'VESPA LX 50 2T'],
            ['model' => 'VESPA LX 50 4T'],
            ['model' => 'VESPA LX 125'],
            ['model' => 'VESPA LX 150'],
            ['model' => 'VESPA LXV 50'],
            ['model' => 'VESPA LXV 125'],
            ['model' => 'VESPA PRIMAVERA 50'],
            ['model' => 'VESPA PRIMAVERA 125'],
            ['model' => 'VESPA PRIMAVERA 150'],
            ['model' => 'VESPA PX 125'],
            ['model' => 'VESPA PX 125 E'],
            ['model' => 'VESPA PX 150'],
            ['model' => 'VESPA PX 150 E'],
            ['model' => 'VESPA PX 200 E'],
            ['model' => 'VESPA S 50'],
            ['model' => 'VESPA S 125'],
            ['model' => 'VESPA S 150'],
            ['model' => 'VESPA SPRINT 50'],
            ['model' => 'VESPA SPRINT 125'],
            ['model' => 'VESPA SPRINT 150'],
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
