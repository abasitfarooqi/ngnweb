<?php

namespace Database\Seeders;

use App\Models\IpRestriction;
use Illuminate\Database\Seeder;

class AllowedIpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add allowed IPs for user ID 113
        $allowedIps = [
            '192.168.1.10', // Example IP
            '192.168.1.11', // Example IP
        ];

        foreach ($allowedIps as $ip) {
            IpRestriction::create([
                'ip_address' => $ip,
                'status' => 'allowed',
                'restriction_type' => 'admin_only',
                'label' => 'Allowed IP for user 113',
                'user_id' => 113,
            ]);
        }
    }
}
