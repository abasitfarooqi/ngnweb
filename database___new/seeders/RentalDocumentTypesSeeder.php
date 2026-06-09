<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class RentalDocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rentalDocuments = [
            [
                'name' => 'Licence Front',
                'slug' => 'licence_front',
                'description' => 'Front side of driving licence',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Licence Back',
                'slug' => 'licence_back',
                'description' => 'Back side of driving licence',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'CBT Certificate',
                'slug' => 'cbt_certificate',
                'description' => 'Valid CBT certificate',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Proof of Address',
                'slug' => 'proof_of_address',
                'description' => 'Recent utility bill, bank statement, or council tax (within 3 months)',
                'is_mandatory' => true,
                'required_for' => ['rental', 'finance'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'Proof of ID',
                'slug' => 'proof_of_id',
                'description' => 'Passport or national ID card',
                'is_mandatory' => true,
                'required_for' => ['rental', 'finance'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 5,
            ],
            [
                'name' => 'Face Picture (Selfie)',
                'slug' => 'face_picture',
                'description' => 'Clear photo of your face',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png'],
                ],
                'sort_order' => 6,
            ],
            [
                'name' => 'Insurance Certificate',
                'slug' => 'insurance_certificate',
                'description' => 'Proof of motorcycle insurance (minimum fire & theft)',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 7,
            ],
            [
                'name' => 'Statement of Facts Insurance',
                'slug' => 'statement_of_facts_insurance',
                'description' => 'Insurance statement of facts',
                'is_mandatory' => false,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 8,
            ],
            [
                'name' => 'Insurance Policy Schedule',
                'slug' => 'insurance_policy_schedule',
                'description' => 'Insurance policy schedule document',
                'is_mandatory' => false,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 9,
            ],
            [
                'name' => 'Rental Agreement',
                'slug' => 'rental_agreement',
                'description' => 'Signed rental agreement (generated)',
                'is_mandatory' => true,
                'required_for' => ['rental'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['pdf'],
                ],
                'sort_order' => 10,
            ],
            [
                'name' => 'Other Document',
                'slug' => 'other_document',
                'description' => 'Any other supporting document',
                'is_mandatory' => false,
                'required_for' => ['rental', 'finance', 'mot', 'recovery'],
                'validation_rules' => [
                    'max_size_mb' => 10,
                    'allowed_formats' => ['jpg', 'jpeg', 'png', 'pdf'],
                ],
                'sort_order' => 99,
            ],
        ];

        foreach ($rentalDocuments as $doc) {
            DocumentType::updateOrCreate(
                ['slug' => $doc['slug']],
                $doc
            );
        }

        $this->command->info('Rental document types seeded successfully!');
    }
}
