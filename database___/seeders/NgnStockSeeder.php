<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ngn_stock')->insert([
            [
                'sku' => '12345',
                'branch_id' => 1, // Assuming branch_id 1 corresponds to the first branch
                'quantity' => 10,
            ],
            [
                'sku' => '12345',
                'branch_id' => 2, // Assuming branch_id 2 corresponds to the second branch
                'quantity' => 20,
            ],
            [
                'sku' => '12346',
                'branch_id' => 1,
                'quantity' => 8,
            ],
            [
                'sku' => '12346',
                'branch_id' => 2,
                'quantity' => 12,
            ],
            [
                'sku' => '12347',
                'branch_id' => 1,
                'quantity' => 25,
            ],
            [
                'sku' => '12347',
                'branch_id' => 2,
                'quantity' => 30,
            ],
            [
                'sku' => '12348',
                'branch_id' => 1,
                'quantity' => 30,
            ],
            [
                'sku' => '12348',
                'branch_id' => 2,
                'quantity' => 25,
            ],
            [
                'sku' => '12349',
                'branch_id' => 1,
                'quantity' => 5,
            ],
            [
                'sku' => '12349',
                'branch_id' => 2,
                'quantity' => 10,
            ],
        ]);
    }
}
