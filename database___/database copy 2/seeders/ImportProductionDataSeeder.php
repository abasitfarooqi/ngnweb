<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportProductionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting production data import...');

        $prodConfig = [
            'host' => '46.101.2.204',
            'database' => 'nqfkhvtysa',
            'username' => 'nqfkhvtysa',
            'password' => 'GZqcny5vRu',
        ];

        $source = new \PDO(
            "mysql:host={$prodConfig['host']};dbname={$prodConfig['database']}",
            $prodConfig['username'],
            $prodConfig['password']
        );

        // Tables to import (in order due to foreign keys)
        $tables = [
            'branches' => 100,
            'vehicle_profiles' => 500,
            'renting_pricings' => 5000,
            'transaction_types' => 50,
            'payment_methods' => 50,
            'customers' => 1000, // Import sample customers
            'judopay_onboardings' => 500,
            'judopay_subscriptions' => 500,
        ];

        foreach ($tables as $table => $limit) {
            $this->importTable($source, $table, $limit);
        }

        $this->command->info('Production data import completed!');
    }

    private function importTable(\PDO $source, string $table, int $limit): void
    {
        $this->command->info("Importing {$table}...");

        try {
            // Check if table exists
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $this->command->warn("Table {$table} does not exist. Skipping...");
                return;
            }

            $stmt = $source->query("SELECT * FROM {$table} LIMIT {$limit}");
            $count = 0;

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                try {
                    DB::table($table)->insertOrIgnore($row);
                    $count++;
                } catch (\Exception $e) {
                    // Skip rows that violate constraints
                    continue;
                }
            }

            $this->command->info("✓ Imported {$count} records to {$table}");
        } catch (\Exception $e) {
            $this->command->error("✗ Error importing {$table}: " . $e->getMessage());
        }
    }
}
