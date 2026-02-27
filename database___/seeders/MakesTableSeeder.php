<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MakesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Aprilia', 'manufacturer_type' => 'OEM'],
            ['name' => 'BSA', 'manufacturer_type' => 'OEM'],
            ['name' => 'Derbi', 'manufacturer_type' => 'OEM'],
            ['name' => 'Gilera', 'manufacturer_type' => 'OEM'],
            ['name' => 'Honda', 'manufacturer_type' => 'OEM'],
            ['name' => 'Husaberg', 'manufacturer_type' => 'OEM'],
            ['name' => 'Husqvarna', 'manufacturer_type' => 'OEM'],
            ['name' => 'Kawasaki', 'manufacturer_type' => 'OEM'],
            ['name' => 'KTM', 'manufacturer_type' => 'OEM'],
            ['name' => 'Moto Guzzi', 'manufacturer_type' => 'OEM'],
            ['name' => 'Piaggio', 'manufacturer_type' => 'OEM'],
            ['name' => 'Piaggio Ape', 'manufacturer_type' => 'OEM'],
            ['name' => 'Scarabeo', 'manufacturer_type' => 'OEM'],
            ['name' => 'Suzuki', 'manufacturer_type' => 'OEM'],
            ['name' => 'Triumph', 'manufacturer_type' => 'OEM'],
            ['name' => 'Vespa', 'manufacturer_type' => 'OEM'],
            ['name' => 'Yamaha', 'manufacturer_type' => 'OEM'],
        ];

        DB::table('makes')->insert($brands);
    }
}
