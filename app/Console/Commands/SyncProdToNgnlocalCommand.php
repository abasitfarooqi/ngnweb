<?php

namespace App\Console\Commands;

use App\Support\ProdToLocalTableSync;
use App\Support\UnifiedSchemaMigration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;

class SyncProdToNgnlocalCommand extends Command
{
    protected $signature = 'db:sync-prod-to-connected
                            {--connection= : Target DB connection name (default: database.default)}
                            {--table= : Sync only this table name}
                            {--permissions-only : Sync only Spatie/permission-related tables}
                            {--compare-schema : Compare production vs connected DB tables/columns only}
                            {--show-schema : Show all table + column names for production and connected DB}
                            {--generate-migration : Generate per-table migrations from production + target-only tables}
                            {--migration-folder=database/migrations/unified-sync : Folder (relative to project root) for generated migration/report files}
                            {--prepare-schema : If production schema is missing/mismatched, run migration folder first}
                            {--deep-clone : Prepare schema if needed, then sync all production data}
                            {--skip-views : Do not recreate views from production}
                            {--force : Allow risky targets (same as source, or non-ngn_clean DB)}';

    protected $aliases = ['db:sync-prod-to-ngnlocal'];

    protected $description = 'Copy structure + data from production MySQL to the connected DB. Set SYNC_PROD_* in .env. Includes permissions/roles tables.';

    public function handle(): int
    {
        $prod = $this->prodConfig();
        if ($prod === null) {
            $this->error('Missing production credentials. Add to .env:');
            $this->line('SYNC_PROD_DB_HOST=');
            $this->line('SYNC_PROD_DB_PORT=3306');
            $this->line('SYNC_PROD_DB_DATABASE=');
            $this->line('SYNC_PROD_DB_USERNAME=');
            $this->line('SYNC_PROD_DB_PASSWORD=');

            return 1;
        }

        $connection = (string) ($this->option('connection') ?: config('database.default', 'mysql'));
        $target = $this->targetConfig($connection);
        if ($target === null) {
            $this->error("Invalid connection [{$connection}] or missing connection credentials.");

            return 1;
        }

        if (($target['database'] ?? '') !== 'ngn_clean' && ! $this->option('force')) {
            $this->error('Target DB database is not "ngn_clean". Refusing to overwrite. Use --force to override.');

            return 1;
        }

        if (
            ! $this->option('force')
            && $this->isSameDatabase($prod, $target)
        ) {
            $this->error('Source and target look identical. Refusing to run to protect production. Use --force only if you are 100% sure.');

            return 1;
        }

        try {
            $src = ProdToLocalTableSync::connectSource($prod);
            $dst = ProdToLocalTableSync::connectTarget($target);
        } catch (\Throwable $e) {
            $this->error('Connection failed: '.$e->getMessage());

            return 1;
        }

        $srcSchema = $prod['database'];
        $dstSchema = $target['database'];

        $this->info("Source DB: {$prod['host']}:{$prod['port']}/{$srcSchema}");
        $this->info("Target DB: {$target['host']}:{$target['port']}/{$dstSchema} (connection: {$connection})");

        if ($this->option('show-schema')) {
            $this->renderSchemaMap($src, $srcSchema, 'Production');
            $this->renderSchemaMap($dst, $dstSchema, 'Target');
        }

        if ($this->option('generate-migration')) {
            $this->generateUnifiedMigrationArtifacts($src, $srcSchema, $dst, $dstSchema);

            if (
                ! $this->option('deep-clone')
                && ! $this->option('prepare-schema')
                && ! $this->option('compare-schema')
                && ! $this->option('table')
                && ! $this->option('permissions-only')
            ) {
                return 0;
            }
        }

        if ($this->option('prepare-schema') || $this->option('deep-clone')) {
            $prepared = $this->prepareSchemaIfNeeded($src, $srcSchema, $dst, $dstSchema);
            if (! $prepared) {
                return 1;
            }
            // reconnect so the command uses fresh schema after migrations.
            try {
                $dst = ProdToLocalTableSync::connectTarget($target);
            } catch (\Throwable $e) {
                $this->error('Could not reconnect target after schema prepare: '.$e->getMessage());

                return 1;
            }

            if (! $this->option('deep-clone') && ! $this->option('compare-schema')) {
                $this->info('Schema prepare completed.');

                return 0;
            }
        }

        if ($this->option('compare-schema')) {
            $this->renderSchemaCompare($src, $srcSchema, $dst, $dstSchema);

            return 0;
        }

        if ($this->option('table')) {
            $table = $this->option('table');
            $this->info("Syncing {$table}...");
            $r = ProdToLocalTableSync::syncTable($src, $dst, $srcSchema, $table);
            $this->info("Done. rows={$r['rows']}, inserted={$r['inserted']}");
            $this->flushPermissionCache();

            return 0;
        }

        if ($this->option('permissions-only')) {
            $tables = ProdToLocalTableSync::permissionRelatedTables();
            $this->info('Syncing permission-related tables: '.implode(', ', $tables));
            foreach ($tables as $table) {
                if (! $this->tableExists($src, $srcSchema, $table)) {
                    $this->warn("Skip (not in prod): {$table}");

                    continue;
                }
                $this->line("Syncing {$table}...");
                $r = ProdToLocalTableSync::syncTable($src, $dst, $srcSchema, $table);
                $this->info("  rows={$r['rows']}");
            }
            $this->flushPermissionCache();

            return 0;
        }

        $tables = $src->query(
            'SELECT table_name FROM information_schema.tables
             WHERE table_schema = '.$src->quote($srcSchema)." AND table_type = 'BASE TABLE'
             ORDER BY table_name"
        )->fetchAll(\PDO::FETCH_COLUMN);

        $this->info('Base tables to sync: '.count($tables));
        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();

        foreach ($tables as $i => $table) {
            try {
                ProdToLocalTableSync::syncTable($src, $dst, $srcSchema, $table);
            } catch (\Throwable $e) {
                $bar->finish();
                $this->newLine();
                $this->error("Failed on {$table}: ".$e->getMessage());

                return 1;
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);

        if (! $this->option('skip-views')) {
            $views = $src->query(
                'SELECT table_name FROM information_schema.tables
                 WHERE table_schema = '.$src->quote($srcSchema)." AND table_type = 'VIEW'
                 ORDER BY table_name"
            )->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($views as $view) {
                try {
                    $row = $src->query('SHOW CREATE VIEW '.ProdToLocalTableSync::qid($view))->fetch(\PDO::FETCH_ASSOC);
                    if (! $row || ! isset($row['Create View'])) {
                        continue;
                    }
                    $sql = $row['Create View'];
                    $sql = preg_replace('/\bDEFINER=`[^`]+`@`[^`]+`\s+/i', '', $sql) ?? $sql;
                    $dst->exec('DROP VIEW IF EXISTS '.ProdToLocalTableSync::qid($view));
                    $dst->exec($sql);
                    $this->line("View recreated: {$view}");
                } catch (\Throwable $e) {
                    $this->warn("View {$view} skipped: ".$e->getMessage());
                }
            }
        }

        $this->info('Full sync finished.');
        $this->flushPermissionCache();

        return 0;
    }

    protected function isSameDatabase(array $source, array $target): bool
    {
        return ($source['host'] ?? null) === ($target['host'] ?? null)
            && (int) ($source['port'] ?? 3306) === (int) ($target['port'] ?? 3306)
            && ($source['database'] ?? null) === ($target['database'] ?? null);
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function targetConfig(string $connection): ?array
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

    protected function renderSchemaMap(\PDO $pdo, string $schema, string $label): void
    {
        $this->newLine();
        $this->info("{$label} schema map ({$schema}):");

        $stmt = $pdo->prepare(
            'SELECT TABLE_NAME, COLUMN_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ?
             ORDER BY TABLE_NAME, ORDINAL_POSITION'
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($rows === false || $rows === []) {
            $this->warn("No tables/columns found in {$label} schema.");

            return;
        }

        $currentTable = null;
        foreach ($rows as $row) {
            $table = (string) ($row['TABLE_NAME'] ?? '');
            $column = (string) ($row['COLUMN_NAME'] ?? '');

            if ($table !== $currentTable) {
                $currentTable = $table;
                $this->line('');
                $this->line(" - {$table}");
            }

            $this->line("    - {$column}");
        }
    }

    protected function renderSchemaCompare(\PDO $src, string $srcSchema, \PDO $dst, string $dstSchema): void
    {
        [$srcTables, $srcColumns] = $this->schemaSnapshot($src, $srcSchema);
        [$dstTables, $dstColumns] = $this->schemaSnapshot($dst, $dstSchema);

        $onlyInSource = array_values(array_diff($srcTables, $dstTables));
        $onlyInTarget = array_values(array_diff($dstTables, $srcTables));

        $this->newLine();
        $this->info('Schema comparison (production -> target):');
        $this->line('Tables in production: '.count($srcTables));
        $this->line('Tables in target: '.count($dstTables));
        $this->line('Tables only in production: '.count($onlyInSource));
        $this->line('Tables only in target: '.count($onlyInTarget));

        if ($onlyInSource !== []) {
            $this->newLine();
            $this->warn('Tables only in production (will be created during full sync):');
            foreach ($onlyInSource as $table) {
                $this->line(" - {$table}");
            }
        }

        if ($onlyInTarget !== []) {
            $this->newLine();
            $this->warn('Tables only in target (full sync keeps these as-is):');
            foreach ($onlyInTarget as $table) {
                $this->line(" - {$table}");
            }
        }

        $commonTables = array_values(array_intersect($srcTables, $dstTables));
        sort($commonTables);
        $columnDiffs = 0;

        foreach ($commonTables as $table) {
            $srcCols = $srcColumns[$table] ?? [];
            $dstCols = $dstColumns[$table] ?? [];
            $missingInTarget = array_values(array_diff($srcCols, $dstCols));
            $extraInTarget = array_values(array_diff($dstCols, $srcCols));

            if ($missingInTarget === [] && $extraInTarget === []) {
                continue;
            }

            $columnDiffs++;
            $this->newLine();
            $this->line("Table: {$table}");

            if ($missingInTarget !== []) {
                $this->line('  Missing in target: '.implode(', ', $missingInTarget));
            }
            if ($extraInTarget !== []) {
                $this->line('  Extra in target: '.implode(', ', $extraInTarget));
            }
        }

        if ($columnDiffs === 0) {
            $this->newLine();
            $this->info('Column-level schema matches for all shared tables.');
        }
    }

    /**
     * @return array{0:list<string>,1:array<string,list<string>>}
     */
    protected function schemaSnapshot(\PDO $pdo, string $schema): array
    {
        $tableStmt = $pdo->prepare(
            "SELECT TABLE_NAME
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'
             ORDER BY TABLE_NAME"
        );
        $tableStmt->execute([$schema]);
        $tables = $tableStmt->fetchAll(\PDO::FETCH_COLUMN);
        $tables = is_array($tables) ? array_values($tables) : [];

        $colStmt = $pdo->prepare(
            'SELECT TABLE_NAME, COLUMN_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ?
             ORDER BY TABLE_NAME, ORDINAL_POSITION'
        );
        $colStmt->execute([$schema]);
        $rows = $colStmt->fetchAll(\PDO::FETCH_ASSOC);

        $columnsByTable = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $table = (string) ($row['TABLE_NAME'] ?? '');
                $column = (string) ($row['COLUMN_NAME'] ?? '');
                if ($table === '' || $column === '') {
                    continue;
                }
                $columnsByTable[$table] ??= [];
                $columnsByTable[$table][] = $column;
            }
        }

        return [$tables, $columnsByTable];
    }

    protected function generateUnifiedMigrationArtifacts(\PDO $src, string $srcSchema, \PDO $dst, string $dstSchema): void
    {
        $folder = (string) ($this->option('migration-folder') ?: 'database/migrations/unified-sync');
        $fullPath = base_path(trim($folder, '/'));

        try {
            $result = UnifiedSchemaMigration::generate($src, $srcSchema, $dst, $dstSchema, $fullPath);
        } catch (\Throwable $e) {
            $this->error('Failed generating unified migration: '.$e->getMessage());

            return;
        }

        $this->newLine();
        $this->info('Unified table migrations generated.');
        $this->line('Migration files: '.count($result['migration_files']));
        $firstFile = $result['migration_files'][0] ?? null;
        if (is_string($firstFile)) {
            $this->line('First file: '.$firstFile);
        }
        $this->line('Schema report: '.$result['report_file']);
        $this->line('Run with: php artisan migrate --path='.trim(str_replace(base_path(), '', $fullPath), '/').' --force');
    }

    protected function prepareSchemaIfNeeded(\PDO $src, string $srcSchema, \PDO $dst, string $dstSchema): bool
    {
        if ($this->productionSchemaMatchesTarget($src, $srcSchema, $dst, $dstSchema)) {
            $this->info('Step 1 skipped: target already has all production tables/columns.');

            return true;
        }

        $folder = (string) ($this->option('migration-folder') ?: 'database/migrations/unified-sync');
        $fullPath = base_path(trim($folder, '/'));
        $relativePath = trim(str_replace(base_path(), '', $fullPath), '/');

        $hasPhpMigrations = is_array(glob($fullPath.'/*.php')) && count(glob($fullPath.'/*.php')) > 0;
        if (! $hasPhpMigrations) {
            $this->error("Migration folder has no generated files: {$fullPath}");
            $this->line('Run with --generate-migration first, then rerun with --prepare-schema or --deep-clone.');

            return false;
        }

        $this->warn('Step 1: schema mismatch detected, running migration folder...');
        $exitCode = Artisan::call('migrate', [
            '--path' => $relativePath,
            '--force' => true,
        ]);
        $this->line(trim(Artisan::output()));

        if ($exitCode !== 0) {
            $this->error('Migration step failed.');

            return false;
        }

        return true;
    }

    protected function productionSchemaMatchesTarget(\PDO $src, string $srcSchema, \PDO $dst, string $dstSchema): bool
    {
        [$srcTables, $srcColumns] = $this->schemaSnapshot($src, $srcSchema);
        [, $dstColumns] = $this->schemaSnapshot($dst, $dstSchema);

        foreach ($srcTables as $table) {
            $srcCols = $srcColumns[$table] ?? [];
            $dstCols = $dstColumns[$table] ?? null;
            if ($dstCols === null) {
                return false;
            }
            if ($srcCols !== $dstCols) {
                return false;
            }
        }

        return true;
    }

    protected function flushPermissionCache(): void
    {
        try {
            if (class_exists(PermissionRegistrar::class)) {
                app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
                $this->info('Spatie permission cache cleared.');
            }
        } catch (\Throwable $e) {
            $this->warn('Could not clear Spatie permission cache: '.$e->getMessage());
        }
    }

    protected function tableExists(\PDO $pdo, string $schema, string $table): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
        );
        $stmt->execute([$schema, $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function prodConfig(): ?array
    {
        $host = env('SYNC_PROD_DB_HOST');
        $database = env('SYNC_PROD_DB_DATABASE');
        $user = env('SYNC_PROD_DB_USERNAME');
        $pass = env('SYNC_PROD_DB_PASSWORD');

        if (! $host || ! $database || $user === null || $pass === null) {
            return null;
        }

        return [
            'host' => $host,
            'port' => (int) (env('SYNC_PROD_DB_PORT', 3306)),
            'database' => $database,
            'username' => $user,
            'password' => $pass,
        ];
    }
}
