<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NgnLocalSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('NgnLocalSnapshotSeeder is deprecated.');
        $this->command?->line('Use: php artisan cloudways-to-digital-ocean:sync-data');
        $this->command?->line('See: CLOUDWAYS_TO_DIGITALOCEAN_SYNC.md');
    }
}
