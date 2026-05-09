<?php

namespace App\Console\Commands;

use App\Support\ProdToLocalTableSync;
use Illuminate\Console\Command;

class ExportNgnCleanOnlySeedCommand extends Command
{
    protected $signature = 'db:export-ngnclean-only-seed
                            {--connection= : Local DB connection name that has ngn_clean (default: database.default)}
                            {--source-db= : Source DB name to snapshot (default: NGN_CLEAN_SEED_SOURCE_DB or ngn_clean)}
                            {--output=database/seeders/data/ngn-clean-only : Output folder (relative to project root)}';

    protected $description = 'Export tables that exist in ngn_clean but not production into JSON seed files for portable replay.';

    public function handle(): int
    {
        $connection = (string) ($this->option('connection') ?: config('database.default', 'mysql'));
        $sourceDb = (string) ($this->option('source-db') ?: env('NGN_CLEAN_SEED_SOURCE_DB', 'ngn_clean'));
        $output = base_path(trim((string) $this->option('output'), '/'));

        $local = $this->connectionConfig($connection);
        $prod = $this->productionConfig();

        if ($local === null) {
            $this->error("Invalid target connection [{$connection}]");

            return 1;
        }
        if ($prod === null) {
            $this->error('Missing production credentials in SYNC_PROD_* env values.');

            return 1;
        }

        $source = $local;
        $source['database'] = $sourceDb;

        try {
            $src = ProdToLocalTableSync::connectSource($source);
            $prd = ProdToLocalTableSync::connectSource($prod);
        } catch (\Throwable $e) {
            $this->error('Connection failed: '.$e->getMessage());

            return 1;
        }

        $sourceTables = $this->baseTables($src, $sourceDb);
        $prodTables = $this->baseTables($prd, $prod['database']);
        $tables = array_values(array_diff($sourceTables, $prodTables));
        sort($tables);

        if (! is_dir($output) && ! mkdir($output, 0755, true) && ! is_dir($output)) {
            $this->error("Cannot create output folder: {$output}");

            return 1;
        }

        foreach (glob($output.'/*.json') ?: [] as $file) {
            @unlink($file);
        }

        $this->info("Source DB: {$sourceDb}");
        $this->info('Tables only in source (not production): '.count($tables));

        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'source_db' => $sourceDb,
            'production_db' => $prod['database'],
            'tables' => [],
        ];

        foreach ($tables as $table) {
            $columns = $this->columns($src, $sourceDb, $table);
            $rows = $src->query('SELECT * FROM '.ProdToLocalTableSync::qid($table))->fetchAll(\PDO::FETCH_ASSOC);
            $payload = [
                'table' => $table,
                'columns' => $columns,
                'rows' => is_array($rows) ? $rows : [],
            ];

            file_put_contents(
                $output.'/'.$table.'.json',
                json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );

            $count = is_array($rows) ? count($rows) : 0;
            $manifest['tables'][] = ['name' => $table, 'rows' => $count];
            $this->line("  {$table}: {$count} rows");
        }

        file_put_contents(
            $output.'/manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->newLine();
        $this->info("Export complete: {$output}");
        $this->line('Run seeder later: php artisan db:seed --class=Database\\Seeders\\NgnCleanOnlySnapshotSeeder');

        return 0;
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function connectionConfig(string $connection): ?array
    {
        $cfg = config("database.connections.{$connection}");
        if (! is_array($cfg)) {
            return null;
        }

        $host = (string) ($cfg['host'] ?? '');
        $database = (string) ($cfg['database'] ?? '');
        $username = (string) ($cfg['username'] ?? '');
        $password = (string) ($cfg['password'] ?? '');

        if ($host === '' || $database === '' || $username === '') {
            return null;
        }

        return [
            'host' => $host,
            'port' => (int) ($cfg['port'] ?? 3306),
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ];
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function productionConfig(): ?array
    {
        $host = env('SYNC_PROD_DB_HOST');
        $database = env('SYNC_PROD_DB_DATABASE');
        $username = env('SYNC_PROD_DB_USERNAME');
        $password = env('SYNC_PROD_DB_PASSWORD');

        if (! $host || ! $database || $username === null || $password === null) {
            return null;
        }

        return [
            'host' => $host,
            'port' => (int) env('SYNC_PROD_DB_PORT', 3306),
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ];
    }

    /**
     * @return list<string>
     */
    protected function baseTables(\PDO $pdo, string $schema): array
    {
        $stmt = $pdo->query(
            'SELECT table_name FROM information_schema.tables
             WHERE table_schema = '.$pdo->quote($schema)." AND table_type = 'BASE TABLE'
             ORDER BY table_name"
        );
        $rows = $stmt ? $stmt->fetchAll(\PDO::FETCH_COLUMN) : [];

        return is_array($rows) ? array_values($rows) : [];
    }

    /**
     * @return list<string>
     */
    protected function columns(\PDO $pdo, string $schema, string $table): array
    {
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
             ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute([$schema, $table]);
        $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return is_array($rows) ? array_values($rows) : [];
    }
}

