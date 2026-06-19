<?php

namespace App\Console\Commands;

use App\Support\NgnCleanSchemaSnapshot;
use App\Support\ProdToLocalTableSync;
use Illuminate\Console\Command;

class ExportNgnCleanSchemaCommand extends Command
{
    protected $signature = 'db:export-ngnclean-schema
                            {--connection= : MySQL connection that has ngn_clean (default: database.default)}
                            {--source-db= : Database name (default: ngn_clean)}
                            {--output= : Output folder (default: database/schema/ngn-clean)}';

    protected $description = 'Export ngn_clean table structure (exact columns + CREATE TABLE) into the repo for deploy without ngn_clean on production.';

    public function handle(): int
    {
        $connection = (string) ($this->option('connection') ?: config('database.default', 'mysql'));
        $sourceDb = (string) ($this->option('source-db') ?: env('NGN_CLEAN_SEED_SOURCE_DB', 'ngn_clean'));
        $output = (string) ($this->option('output') ?: '');
        $root = $output !== ''
            ? (str_starts_with($output, '/') ? $output : base_path($output))
            : NgnCleanSchemaSnapshot::defaultPath();

        $cfg = config("database.connections.{$connection}");
        if (! is_array($cfg)) {
            $this->error("Invalid connection [{$connection}]");

            return 1;
        }

        $config = [
            'host' => (string) ($cfg['host'] ?? ''),
            'port' => (int) ($cfg['port'] ?? 3306),
            'database' => $sourceDb,
            'username' => (string) ($cfg['username'] ?? ''),
            'password' => (string) ($cfg['password'] ?? ''),
        ];

        try {
            $pdo = ProdToLocalTableSync::connectSource($config);
        } catch (\Throwable $e) {
            $this->error('Connection failed: '.$e->getMessage());

            return 1;
        }

        $this->info("Exporting schema from {$config['host']}:{$config['port']}/{$sourceDb}");
        $this->info("Output: {$root}");

        try {
            $result = NgnCleanSchemaSnapshot::exportFromDatabase($pdo, $sourceDb, $root);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info('Exported '.$result['tables'].' tables.');
        $this->line('Commit database/schema/ngn-clean/ then deploy.');
        $this->line('On any server: php artisan cloudways-to-digital-ocean:sync-data');

        return 0;
    }
}
