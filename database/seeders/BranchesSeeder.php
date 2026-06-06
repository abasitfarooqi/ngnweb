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
                'address' => 'NGN 9-13 Catford Hill, London SE6 4NU',
                'postcode' => 'SE6 4NU',
                'phone' => '020 XXXX XXXX',
                'email' => 'catford@ngnmotor.co.uk',
                'opening_hours' => json_encode([
                    'monday'    => '09:00-18:00',
                    'tuesday'   => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday'  => '09:00-18:00',
                    'friday'    => '09:00-18:00',
                    'saturday'  => '09:00-15:45',
                    'sunday'    => 'Closed',
                ]),
            ],
            [
                'id' => 2,
                'name' => 'Tooting',
                'city' => 'London',
                'address' => 'NGN 4A Penwortham Road, London SW16 6RE',
                'postcode' => 'SW16 6RE',
                'phone' => '020 XXXX XXXX',
                'email' => 'tooting@ngnmotor.co.uk',
                'opening_hours' => json_encode([
                    'monday'    => '09:00-18:00',
                    'tuesday'   => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday'  => '09:00-18:00',
                    'friday'    => '09:00-18:00',
                    'saturday'  => '09:00-15:45',
                    'sunday'    => 'Closed',
                ]),
            ],
            [
                'id' => 3,
                'name' => 'Sutton',
                'city' => 'London',
                'address' => 'NGN 329 High St, Sutton SM1 1LW',
                'postcode' => 'SM1 1LW',
                'phone' => '020 XXXX XXXX',
                'email' => 'sutton@ngnmotor.co.uk',
                'opening_hours' => json_encode([
                    'monday'    => '09:00-18:00',
                    'tuesday'   => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday'  => '09:00-18:00',
                    'friday'    => '09:00-18:00',
                    'saturday'  => '09:00-15:45',
                    'sunday'    => 'Closed',
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
