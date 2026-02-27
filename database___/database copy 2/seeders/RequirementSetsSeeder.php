<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequirementSetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requirementSets = [
            [
                'name' => 'Rental',
                'slug' => 'rental',
                'description' => 'Requirements for renting a motorcycle',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Requirements for finance application',
                'is_active' => true,
            ],
            [
                'name' => 'MOT Booking',
                'slug' => 'mot_booking',
                'description' => 'Requirements for MOT booking',
                'is_active' => true,
            ],
            [
                'name' => 'Test Ride',
                'slug' => 'test_ride',
                'description' => 'Requirements for test ride',
                'is_active' => true,
            ],
        ];

        foreach ($requirementSets as $set) {
            DB::table('requirement_sets')->updateOrInsert(
                ['slug' => $set['slug']],
                array_merge($set, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Get the rental requirement set ID
        $rentalSet = DB::table('requirement_sets')->where('slug', 'rental')->first();
        $financeSet = DB::table('requirement_sets')->where('slug', 'finance')->first();

        // Define requirements for rental
        $rentalRequirements = [
            [
                'requirement_set_id' => $rentalSet->id,
                'type' => 'document_required',
                'key' => 'driving_licence',
                'label' => 'Driving Licence',
                'description' => 'Valid UK or international driving licence',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'requirement_set_id' => $rentalSet->id,
                'type' => 'document_required',
                'key' => 'proof_of_address',
                'label' => 'Proof of Address',
                'description' => 'Recent utility bill or bank statement',
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            [
                'requirement_set_id' => $rentalSet->id,
                'type' => 'document_required',
                'key' => 'proof_of_id',
                'label' => 'Proof of ID',
                'description' => 'Passport or national ID card',
                'is_mandatory' => true,
                'sort_order' => 3,
            ],
            [
                'requirement_set_id' => $rentalSet->id,
                'type' => 'document_required',
                'key' => 'cbt_certificate',
                'label' => 'CBT Certificate',
                'description' => 'Valid CBT certificate',
                'is_mandatory' => true,
                'sort_order' => 4,
            ],
            [
                'requirement_set_id' => $rentalSet->id,
                'type' => 'document_required',
                'key' => 'insurance_certificate',
                'label' => 'Insurance Certificate',
                'description' => 'Proof of motorcycle insurance (minimum fire & theft)',
                'is_mandatory' => true,
                'sort_order' => 5,
            ],
            [
                'requirement_set_id' => $rentalSet->id,
                'type' => 'field_required',
                'key' => 'min_age',
                'label' => 'Minimum Age',
                'description' => 'Must be 18 years or older',
                'validation_rules' => json_encode(['min_age' => 18]),
                'is_mandatory' => true,
                'sort_order' => 6,
            ],
        ];

        // Define requirements for finance
        $financeRequirements = [
            [
                'requirement_set_id' => $financeSet->id,
                'type' => 'document_required',
                'key' => 'proof_of_id',
                'label' => 'Proof of ID',
                'description' => 'Passport or national ID card',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'requirement_set_id' => $financeSet->id,
                'type' => 'document_required',
                'key' => 'proof_of_address',
                'label' => 'Proof of Address',
                'description' => 'Recent utility bill or bank statement',
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            [
                'requirement_set_id' => $financeSet->id,
                'type' => 'document_required',
                'key' => 'proof_of_income',
                'label' => 'Proof of Income',
                'description' => 'Payslip or self-assessment tax return',
                'is_mandatory' => false,
                'sort_order' => 3,
            ],
        ];

        foreach (array_merge($rentalRequirements, $financeRequirements) as $requirement) {
            DB::table('requirements')->updateOrInsert(
                [
                    'requirement_set_id' => $requirement['requirement_set_id'],
                    'key' => $requirement['key'],
                ],
                array_merge($requirement, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Requirement sets and requirements seeded successfully!');
    }
}
