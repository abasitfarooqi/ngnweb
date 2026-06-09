<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create first admin user
        $admin1 = User::create([
            'name' => 'Admin One',
            'email' => 'admin1@neguinhomotors.co.uk',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin1->assignRole('admin');

        // Create second admin user
        $admin2 = User::create([
            'name' => 'Admin Two',
            'email' => 'admin2@neguinhomotors.co.uk',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin2->assignRole('admin');

        $this->command->info('Two admin users created successfully!');
        $this->command->info('Admin 1: admin1@neguinhomotors.co.uk / password123');
        $this->command->info('Admin 2: admin2@neguinhomotors.co.uk / password123');
    }
}
