<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if branches already exist
        $existingCount = DB::table('branches')->count();

        if ($existingCount >= 3) {
            $this->command->info('Branches already exist. Skipping seeding.');
            return;
        }

        $branches = [
            [
                'id' => 1,
                'name' => 'Catford',
                'city' => 'London',
                'address' => 'NGN Motor Catford Branch Address',
                'postcode' => 'SE6 XXX',
                'phone' => '020 XXXX XXXX',
                'email' => 'catford@ngnmotor.co.uk',
                'opening_hours' => json_encode([
                    'monday' => '09:00-18:00',
                    'tuesday' => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday' => '09:00-18:00',
                    'friday' => '09:00-18:00',
                    'saturday' => '09:00-18:00',
                    'sunday' => 'Closed',
                ]),
            ],
            [
                'id' => 2,
                'name' => 'Tooting',
                'city' => 'London',
                'address' => 'NGN Motor Tooting Branch Address',
                'postcode' => 'SW17 XXX',
                'phone' => '020 XXXX XXXX',
                'email' => 'tooting@ngnmotor.co.uk',
                'opening_hours' => json_encode([
                    'monday' => '09:00-18:00',
                    'tuesday' => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday' => '09:00-18:00',
                    'friday' => '09:00-18:00',
                    'saturday' => '09:00-18:00',
                    'sunday' => 'Closed',
                ]),
            ],
            [
                'id' => 3,
                'name' => 'Sutton',
                'city' => 'London',
                'address' => 'NGN Motor Sutton Branch Address',
                'postcode' => 'SM1 XXX',
                'phone' => '020 XXXX XXXX',
                'email' => 'sutton@ngnmotor.co.uk',
                'opening_hours' => json_encode([
                    'monday' => '09:00-18:00',
                    'tuesday' => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday' => '09:00-18:00',
                    'friday' => '09:00-18:00',
                    'saturday' => '09:00-18:00',
                    'sunday' => 'Closed',
                ]),
            ],
        ];

        foreach ($branches as $branch) {
            DB::table('branches')->updateOrInsert(
                ['id' => $branch['id']],
                array_merge($branch, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Branches seeded successfully!');
    }
}
