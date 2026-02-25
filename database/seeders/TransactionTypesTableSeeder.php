<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $transactionTypes = [
            ['type' => 'Deposit',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            ['type' => 'Rental Fee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            ['type' => 'Deposit & Rental Fee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            ['type' => 'Late Fee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            ['type' => 'Damages Fee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            ['type' => 'Refund',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            ['type' => 'Adjustment',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ];

        DB::table('transaction_types')->insert($transactionTypes);
    }
}
