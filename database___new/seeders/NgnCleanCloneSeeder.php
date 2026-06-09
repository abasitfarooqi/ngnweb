<?php

namespace Database\Seeders;

use App\Support\ProdToLocalTableSync;
use Illuminate\Database\Seeder;

class NgnCleanCloneSeeder extends Seeder
{
    public function run(): void
    {
        $connection = (string) env('NGN_CLONE_TARGET_CONNECTION', config('database.default', 'mysql'));
        $target = $this->connectionConfig($connection);
        if ($target === null) {
            $this->command?->error("Invalid target connection: {$connection}");

            return;
        }

        $sourceDb = (string) env('NGN_CLEAN_SEED_SOURCE_DB', 'ngn_clean');
        if ($sourceDb === '') {
            $this->command?->error('NGN_CLEAN_SEED_SOURCE_DB is empty.');

            return;
        }

        $source = $target;
        $source['database'] = $sourceDb;

        if ($source['database'] === $target['database']) {
            $this->command?->warn('Source and target DB are the same. Nothing to clone.');

            return;
        }

        $prod = $this->productionConfig();
        if ($prod === null) {
            $this->command?->error('Missing production credentials: SYNC_PROD_DB_HOST, SYNC_PROD_DB_DATABASE, SYNC_PROD_DB_USERNAME, SYNC_PROD_DB_PASSWORD');

            return;
        }

        try {
            $src = ProdToLocalTableSync::connectSource($source);
            $dst = ProdToLocalTableSync::connectTarget($target);
            $prodPdo = ProdToLocalTableSync::connectSource($prod);
        } catch (\Throwable $e) {
            $this->command?->error('DB connection failed: '.$e->getMessage());

            return;
        }

        $tables = $this->baseTables($src, $sourceDb);
        $productionTables = $this->baseTables($prodPdo, $prod['database']);
        $tables = array_values(array_diff($tables, $productionTables));
        sort($tables);

        if (! (bool) env('NGN_CLEAN_SEED_INCLUDE_MIGRATIONS', false)) {
            $tables = array_values(array_filter($tables, fn (string $t): bool => $t !== 'migrations'));
        }

        $this->command?->info("Cloning ngn_clean-only (non-production) tables from [{$sourceDb}] to [{$target['database']}] on connection [{$connection}]");
        $this->command?->info('Tables to clone (not in production): '.count($tables));

        if ($tables === []) {
            $this->command?->info('No ngn_clean-only tables to clone.');

            return;
        }

        foreach ($tables as $table) {
            try {
                $result = ProdToLocalTableSync::syncTable($src, $dst, $sourceDb, $table);
                $this->command?->line("  {$table}: rows={$result['rows']}, inserted={$result['inserted']}");
            } catch (\Throwable $e) {
                $this->command?->warn("  {$table} skipped: ".$e->getMessage());
            }
        }

        $this->command?->info('Ngn clean clone seeding finished.');
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
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function connectionConfig(string $connection): ?array
    {
        $cfg = config("database.connections.{$connection}");
        if (! is_array($cfg)) {
            return null;
        }

        $host = isset($cfg['host']) ? (string) $cfg['host'] : '';
        $database = isset($cfg['database']) ? (string) $cfg['database'] : '';
        $username = isset($cfg['username']) ? (string) $cfg['username'] : '';
        $password = isset($cfg['password']) ? (string) $cfg['password'] : '';

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
}

