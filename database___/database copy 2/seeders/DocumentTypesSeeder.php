<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'Driving Licence',
                'slug' => 'driving_licence',
                'description' => 'Valid UK or international driving licence',
                'is_mandatory' => true,
                'required_for' => ['rental', 'test_ride'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Proof of Address',
                'slug' => 'proof_of_address',
                'description' => 'Recent utility bill, bank statement, or council tax bill (within last 3 months)',
                'is_mandatory' => true,
                'required_for' => ['rental', 'finance'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Proof of ID',
                'slug' => 'proof_of_id',
                'description' => 'Passport, national ID card, or other government-issued ID',
                'is_mandatory' => true,
                'required_for' => ['rental', 'finance'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'CBT Certificate',
                'slug' => 'cbt_certificate',
                'description' => 'Valid CBT certificate (if applicable)',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'Insurance Certificate',
                'slug' => 'insurance_certificate',
                'description' => 'Proof of motorcycle insurance (minimum fire & theft)',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 5,
            ],
            [
                'name' => 'Bank Statement',
                'slug' => 'bank_statement',
                'description' => 'Recent bank statement (within last 3 months)',
                'is_mandatory' => false,
                'required_for' => ['finance'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 6,
            ],
            [
                'name' => 'Proof of Income',
                'slug' => 'proof_of_income',
                'description' => 'Payslip, P60, or self-assessment tax return',
                'is_mandatory' => false,
                'required_for' => ['finance'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 7,
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }

        $this->command->info('Document types seeded successfully!');
    }
}
