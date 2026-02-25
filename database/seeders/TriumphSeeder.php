<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriumphSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hondaId = DB::table('brand_names')->where('name', 'Triumph')->first()->id;

        $bikeModels = [
            ['model' => 'ADVENTURER (from VIN 71699)'],
            ['model' => 'ADVENTURER (to VIN 71698)'],
            ['model' => 'AMERICA (Carb models)'],
            ['model' => 'AMERICA (EFI models)'],
            ['model' => 'BONNEVILLE & SE MODELS (from VIN 380777)'],
            ['model' => 'BONNEVILLE & T100 (carb models)'],
            ['model' => 'BONNEVILLE & T100 (EFI Models)'],
            ['model' => 'BONNEVILLE BOBBER'],
            ['model' => 'BONNEVILLE T120'],
            ['model' => 'BONNEVILLE T120 BLACK'],
            ['model' => 'DAYTONA 595 / 955i'],
            ['model' => 'DAYTONA 600 & 650'],
            ['model' => 'DAYTONA 675 (From VIN 381275 to VIN 564947)'],
            ['model' => 'DAYTONA 675 (to VIN 381274)'],
            ['model' => 'DAYTONA 675R (from VIN 564948)'],
            ['model' => 'DAYTONA 750/1000'],
            ['model' => 'DAYTONA 955i (From VIN 132513)'],
            ['model' => 'DAYTONA 1200, 900 & SUPER III'],
            ['model' => 'LEGEND TT'],
            ['model' => 'ROCKET III, CLASSIC & ROADSTER'],
            ['model' => 'ROCKET III TOURING'],
            ['model' => 'SCRAMBLER (carb models)'],
            ['model' => 'SCRAMBLER (EFi models)'],
            ['model' => 'SPEED FOUR'],
            ['model' => 'SPEEDMASTER (carb models)'],
            ['model' => 'SPEEDMASTER (EFi models)'],
            ['model' => 'SPEED TRIPLE (carbs)'],
            ['model' => 'SPEED TRIPLE (From VIN 141872 to VIN 210444)'],
            ['model' => 'SPEED TRIPLE (From VIN 210445 to VIN 461331)'],
            ['model' => 'SPEED TRIPLE (From VIN 461332 to VIN 735437)'],
            ['model' => "SPEED TRIPLE 885cc\955cc (EFI) (Up to VIN 141871)"],
            ['model' => 'SPEED TRIPLE R (From VIN 735337)'],
            ['model' => 'SPEED TRIPLE R (To VIN 735336)'],
            ['model' => 'SPEED TRIPLE S (From VIN 735438 to VIN 867684)'],
            ['model' => 'SPRINT (carb models)'],
            ['model' => 'SPRINT GT'],
            ['model' => 'SPRINT RS (From VIN 139277)'],
            ['model' => 'SPRINT RS (To VIN 139276)'],
            ['model' => 'SPRINT ST (From VIN 139277 to VIN 208166)'],
            ['model' => 'SPRINT ST (From VIN 208167)'],
            ['model' => 'SPRINT ST (To VIN 139276)'],
            ['model' => 'STREET CUP'],
            ['model' => 'STREET SCRAMBLER'],
            ['model' => 'STREET TRIPLE (from VIN 560477)'],
            ['model' => 'STREET TRIPLE (To VIN 560477)'],
            ['model' => 'STREET TRIPLE R (From VIN 560477)'],
            ['model' => 'STREET TRIPLE R (from VIN 806646)'],
            ['model' => 'STREET TRIPLE R (To VIN 560476)'],
            ['model' => 'STREET TRIPLE RS (from VIN 800262)'],
            ['model' => 'STREET TRIPLE S (from VIN 803572)'],
            ['model' => 'STREET TWIN'],
            ['model' => 'THRUXTON (Liquid Cooled)'],
            ['model' => 'THRUXTON 900 (carb models)'],
            ['model' => 'THRUXTON 900 (efI models)'],
            ['model' => 'THRUXTON R (Liquid Cooled)'],
            ['model' => 'THUNDERBIRD'],
            ['model' => 'THUNDERBIRD (1600/1700)'],
            ['model' => 'THUNDERBIRD SPORT'],
            ['model' => 'THUNDERBIRD STORM'],
            ['model' => 'TIGER 800 (To VIN 674841)'],
            ['model' => 'TIGER 800 XC (To VIN 674841)'],
            ['model' => 'TIGER 800 XCA (From VIN 674842)'],
            ['model' => 'TIGER 800 XCx (From VIN 674842)'],
            ['model' => 'TIGER 800 XR (From VIN 674842)'],
            ['model' => 'TIGER 800 XRT (From VIN 674842)'],
            ['model' => 'TIGER 800 XRx (From VIN 674842)'],
            ['model' => 'TIGER 885 (Carb models to VIN 71698)'],
            ['model' => 'TIGER 885i (From VIN 71699 to VIN 124105)'],
            ['model' => 'TIGER 955i (From VIN 124106 to VIN 198874)'],
            ['model' => 'TIGER 955i (From VIN 198875 to VIN 287503)'],
            ['model' => 'TIGER 1050 (From VIN 287504 to VIN 570058)'],
            ['model' => 'TIGER 1200 Explorer & XC (To VIN 740276)'],
            ['model' => 'TIGER 1215 EXPLORER XCA FROM VIN 740277'],
            ['model' => 'TIGER 1215 EXPLORER XC FROM VIN 740277'],
            ['model' => 'TIGER 1215 EXPLORER XCx FROM VIN 740277'],
            ['model' => 'TIGER 1215 EXPLORER XR FROM VIN 740277'],
            ['model' => 'TIGER 1215 EXPLORER XRT FROM 740277'],
            ['model' => 'TIGER 1215 EXPLORER XRx FROM VIN 740277'],
            ['model' => 'TIGER SPORT (From VIN 570059 to 750469)'],
            ['model' => 'TIGER SPORT (VIN from 750470)'],
            ['model' => 'TRIDENT'],
            ['model' => 'TROPHY (From VIN 29156)'],
            ['model' => 'TROPHY (To VIN 29155)'],
            ['model' => 'TROPHY 1215'],
            ['model' => 'TROPHY 1215 SE'],
            ['model' => 'TT600'],
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
