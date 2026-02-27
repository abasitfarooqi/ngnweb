<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypesSeeder extends Seeder
{
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'Licence Front',
                'code' => 'licence_front',
                'description' => 'Front side of the driving license',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'License Back',
                'code' => 'licence_back',
                'description' => 'Back side of the driving license',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CBT Certificate',
                'code' => 'cbt_certificate',
                'description' => 'Compulsory Basic Training certificate. If not full license holder',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Proof of Address',
                'code' => 'proof_of_address',
                'description' => 'Utility bill or bank statement not older than 3 months',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Proof of ID',
                'code' => 'proof_of_id',
                'description' => 'Passport or National ID card or BRP',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Proof of Insurance',
                'code' => 'proof_of_insurance',
                'description' => 'Insurance document showing coverage',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Other Document',
                'code' => 'other_doc',
                'description' => 'Other Document, Guaranter or reference',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Insurance Certificate',
                'code' => 'insurance_certificate',
                'description' => 'Insurance certificate showing coverage',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Insurance Policy',
                'code' => 'insurance_policy',
                'description' => 'Insurance policy document showing coverage',
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($documentTypes as $type) {
            DB::table('document_types')->insert($type);
        }
    }
}
