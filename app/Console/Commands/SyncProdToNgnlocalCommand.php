<?php

namespace App\Console\Commands;

use App\Support\NgnCleanSchemaSnapshot;
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
                            {--seed-local-extra : Also sync non-production tables from local seed DB (override + preserve PK IDs)}
                            {--seed-source-db= : Local seed source DB name for non-production tables (default: ngn_clean)}
                            {--schema-snapshot : Apply committed ngn_clean schema from database/schema/ngn-clean (works on production without ngn_clean DB)}
                            {--schema-snapshot-path= : Override snapshot folder (default: database/schema/ngn-clean)}
                            {--schema-from-db= : Use live ngn_clean DB for structure instead of committed snapshot (local only)}
                            {--refresh-schema-snapshot : Re-export database/schema/ngn-clean from live ngn_clean before sync}
                            {--use-production-schema : Use production table structure (legacy; not recommended)}
                            {--compare-vs-ngnclean : List columns/tables production is missing vs ngn_clean schema}
                            {--skip-views : Do not recreate views from production}
                            {--force : Allow risky targets (same as source, or non-ngn_clean DB)}';

    protected $aliases = ['db:sync-prod-to-ngnlocal'];

    protected $description = 'Overwrite target with ngn_clean schema (committed snapshot) + production row data. Export schema locally: db:export-ngnclean-schema';

    /** @var array<string, array{columns:list<string>, create_sql:string}>|null */
    protected ?array $schemaSnapshotTables = null;

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

        $this->info("Data source (production): {$prod['host']}:{$prod['port']}/{$srcSchema}");
        $this->info("Target DB: {$target['host']}:{$target['port']}/{$dstSchema} (connection: {$connection})");

        if ($this->option('refresh-schema-snapshot')) {
            if ($this->exportSchemaSnapshot($connection) !== 0) {
                return 1;
            }
        }

        if ($this->option('compare-vs-ngnclean')) {
            $schemaPdo = null;
            $schemaSchema = '';
            if (! $this->resolveCanonicalSchema($connection, $schemaPdo, $schemaSchema)) {
                return 1;
            }
            if ($this->schemaSnapshotTables !== null) {
                $this->renderSchemaCompareProdVsSnapshot($src, $srcSchema, $this->schemaSnapshotTables);
            } elseif ($schemaPdo !== null) {
                $this->renderSchemaCompareProdVsNgnClean($src, $srcSchema, $schemaPdo, $schemaSchema);
            } else {
                $this->error('No ngn_clean schema available. Run: php artisan db:export-ngnclean-schema');

                return 1;
            }

            return 0;
        }

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

        $schemaPdo = null;
        $schemaSchema = '';
        $needsCanonicalSchema = $this->option('prepare-schema')
            || $this->option('deep-clone')
            || $this->option('table')
            || $this->option('permissions-only')
            || (! $this->option('compare-schema') && ! $this->option('show-schema') && ! $this->option('generate-migration'));

        if ($needsCanonicalSchema && ! $this->resolveCanonicalSchema($connection, $schemaPdo, $schemaSchema)) {
            return 1;
        }

        if ($this->option('prepare-schema') || $this->option('deep-clone')) {
            $prepared = $this->prepareSchemaIfNeeded($src, $srcSchema, $dst, $dstSchema, $schemaPdo, $schemaSchema);
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
            $r = $this->syncOneTable($src, $srcSchema, $schemaPdo, $schemaSchema, $dst, $table);
            $this->info("Done. rows={$r['rows']}, inserted={$r['inserted']}, schema_only_columns={$r['schema_only_columns']}");
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
                $r = $this->syncOneTable($src, $srcSchema, $schemaPdo, $schemaSchema, $dst, $table);
                $this->info("  rows={$r['rows']}");
            }
            $this->flushPermissionCache();

            return 0;
        }

        $tables = $this->canonicalTableList($src, $srcSchema, $schemaPdo, $schemaSchema);

        if ($this->schemaSnapshotTables !== null || $schemaPdo !== null) {
            $prodOnly = array_values(array_diff($this->baseTables($src, $srcSchema), $tables));
            if ($prodOnly !== []) {
                $this->warn('Tables in production but not in ngn_clean schema (skipped): '.count($prodOnly));
            }
        }

        $this->info('Base tables to sync: '.count($tables));
        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();

        foreach ($tables as $i => $table) {
            try {
                $this->syncOneTable($src, $srcSchema, $schemaPdo, $schemaSchema, $dst, $table);
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

        if ($this->option('seed-local-extra')) {
            $this->syncLocalOnlyTables($prod, $target, $srcSchema, $dstSchema);
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

    /**
     * @return array{rows:int, inserted:int, schema_only_columns:int}
     */
    protected function syncOneTable(
        \PDO $dataSrc,
        string $dataSchema,
        ?\PDO $schemaPdo,
        string $schemaSchema,
        \PDO $dst,
        string $table
    ): array {
        if ($this->schemaSnapshotTables !== null) {
            $meta = $this->schemaSnapshotTables[$table] ?? null;
            if ($meta === null) {
                throw new \RuntimeException('Table '.$table.' missing from ngn_clean schema snapshot');
            }

            return NgnCleanSchemaSnapshot::applyTableToTarget($dataSrc, $dataSchema, $dst, $table, $meta);
        }

        if ($schemaPdo !== null && $schemaSchema !== '') {
            return ProdToLocalTableSync::syncTableWithSchemaFrom(
                $dataSrc,
                $dataSchema,
                $schemaPdo,
                $schemaSchema,
                $dst,
                $table
            );
        }

        return ProdToLocalTableSync::syncTable($dataSrc, $dst, $dataSchema, $table);
    }

    protected function prepareSchemaIfNeeded(
        \PDO $src,
        string $srcSchema,
        \PDO $dst,
        string $dstSchema,
        ?\PDO $schemaPdo = null,
        string $schemaSchema = ''
    ): bool {
        $schemaMatches = $this->schemaSnapshotTables !== null
            ? $this->snapshotSchemaMatchesTarget($this->schemaSnapshotTables, $dst, $dstSchema)
            : ($schemaPdo !== null && $schemaSchema !== ''
                ? $this->canonicalSchemaMatchesTarget($schemaPdo, $schemaSchema, $dst, $dstSchema)
                : $this->productionSchemaMatchesTarget($src, $srcSchema, $dst, $dstSchema));

        if ($schemaMatches) {
            $this->info('Step 1 skipped: target already matches canonical schema.');

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

    protected function syncLocalOnlyTables(array $prod, array $target, string $srcSchema, string $dstSchema): void
    {
        $seedDb = (string) ($this->option('seed-source-db') ?: env('SYNC_LOCAL_SEED_DB', 'ngn_clean'));
        if ($seedDb === '') {
            $this->warn('Skip local-extra sync: empty seed source DB name.');

            return;
        }

        $seed = $target;
        $seed['database'] = $seedDb;

        if ($this->isSameDatabase($seed, $target)) {
            $this->warn('Skip local-extra sync: seed source DB matches target DB.');

            return;
        }

        try {
            $seedPdo = ProdToLocalTableSync::connectSource($seed);
            $dstPdo = ProdToLocalTableSync::connectTarget($target);
            $prodPdo = ProdToLocalTableSync::connectSource($prod);
        } catch (\Throwable $e) {
            $this->warn('Skip local-extra sync: '.$e->getMessage());

            return;
        }

        $prodTables = $this->baseTables($prodPdo, $srcSchema);
        $seedTables = $this->baseTables($seedPdo, $seedDb);
        $extraTables = array_values(array_diff($seedTables, $prodTables));
        sort($extraTables);

        if ($extraTables === []) {
            $this->info("No local-only tables found in seed DB [{$seedDb}].");

            return;
        }

        $this->info('Syncing local-only tables from seed DB ['.$seedDb.']: '.count($extraTables));
        foreach ($extraTables as $table) {
            try {
                $r = ProdToLocalTableSync::syncTable($seedPdo, $dstPdo, $seedDb, $table);
                $this->line("  {$table}: rows={$r['rows']}, inserted={$r['inserted']}");
            } catch (\Throwable $e) {
                $this->warn("  {$table} skipped: ".$e->getMessage());
            }
        }
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

    protected function canonicalSchemaMatchesTarget(\PDO $schema, string $schemaDb, \PDO $dst, string $dstSchema): bool
    {
        [$schemaTables, $schemaColumns] = $this->schemaSnapshot($schema, $schemaDb);
        [, $dstColumns] = $this->schemaSnapshot($dst, $dstSchema);

        foreach ($schemaTables as $table) {
            $expected = $schemaColumns[$table] ?? [];
            $actual = $dstColumns[$table] ?? null;
            if ($actual === null) {
                return false;
            }
            if ($expected !== $actual) {
                return false;
            }
        }

        return true;
    }

    protected function resolveCanonicalSchema(string $connection, ?\PDO &$schemaPdo, string &$schemaSchema): bool
    {
        $this->schemaSnapshotTables = null;
        $schemaPdo = null;
        $schemaSchema = '';

        if ($this->option('use-production-schema')) {
            $this->warn('Using production schema for table structure (legacy mode).');

            return true;
        }

        $schemaFromDb = trim((string) $this->option('schema-from-db'));

        if ($schemaFromDb === '') {
            $root = $this->schemaSnapshotPath();
            if (! is_file(NgnCleanSchemaSnapshot::manifestPath($root))) {
                $this->error('Committed ngn_clean schema snapshot not found: '.$root);
                $this->line('On your Mac (where ngn_clean lives): php artisan db:export-ngnclean-schema');
                $this->line('Commit database/schema/ngn-clean/ then deploy.');
                $this->line('Or for local-only sync: --schema-from-db=ngn_clean');

                return false;
            }

            try {
                $manifest = NgnCleanSchemaSnapshot::load($root);
                $this->schemaSnapshotTables = $manifest['tables'];
                $this->info('Schema source (committed snapshot): '.$root);
                if ($manifest['generated_at'] !== '') {
                    $this->line('Snapshot generated: '.$manifest['generated_at']);
                }

                return true;
            } catch (\Throwable $e) {
                $this->error($e->getMessage());

                return false;
            }
        }

        $dbName = $schemaFromDb;
        $schemaConfig = $this->schemaConfig($connection, $dbName);
        if ($schemaConfig === null) {
            $this->error("Could not connect to schema DB [{$dbName}].");
            $this->line('On production, commit database/schema/ngn-clean and use --schema-snapshot (default).');
            $this->line('Locally: php artisan db:export-ngnclean-schema');

            return false;
        }

        try {
            $schemaPdo = ProdToLocalTableSync::connectSource($schemaConfig);
        } catch (\Throwable $e) {
            $this->error('Schema DB connection failed: '.$e->getMessage());

            return false;
        }
        $schemaSchema = $dbName;
        $this->info("Schema source (live DB): {$schemaConfig['host']}:{$schemaConfig['port']}/{$schemaSchema}");

        return true;
    }

    protected function schemaSnapshotPath(): string
    {
        $path = trim((string) $this->option('schema-snapshot-path'));

        return $path !== ''
            ? (str_starts_with($path, '/') ? $path : base_path($path))
            : NgnCleanSchemaSnapshot::defaultPath();
    }

    protected function exportSchemaSnapshot(string $connection): int
    {
        $sourceDb = trim((string) $this->option('schema-from-db'));
        if ($sourceDb === '') {
            $sourceDb = (string) ($this->option('seed-source-db') ?: env('SYNC_LOCAL_SEED_DB', 'ngn_clean'));
        }

        return Artisan::call('db:export-ngnclean-schema', [
            '--connection' => $connection,
            '--source-db' => $sourceDb,
            '--output' => $this->schemaSnapshotPath(),
        ]) === 0 ? 0 : 1;
    }

    /**
     * @param  array<string, array{columns:list<string>, create_sql:string}>  $snapshotTables
     */
    protected function renderSchemaCompareProdVsSnapshot(\PDO $prod, string $prodSchema, array $snapshotTables): void
    {
        [$prodTables, $prodColumns] = $this->schemaSnapshot($prod, $prodSchema);
        $ngnTables = array_keys($snapshotTables);
        sort($ngnTables);

        $onlyNgn = array_values(array_diff($ngnTables, $prodTables));
        $common = array_values(array_intersect($prodTables, $ngnTables));
        sort($common);

        $this->newLine();
        $this->info("Schema diff: production [{$prodSchema}] vs committed ngn_clean snapshot");
        $this->line('Tables only in ngn_clean snapshot: '.count($onlyNgn));

        $missingOnProd = 0;
        foreach ($common as $table) {
            $ngnCols = $snapshotTables[$table]['columns'] ?? [];
            $missing = array_values(array_diff($ngnCols, $prodColumns[$table] ?? []));
            if ($missing === []) {
                continue;
            }
            $missingOnProd++;
            $this->newLine();
            $this->warn("{$table} — columns missing on production:");
            foreach ($missing as $col) {
                $this->line("  - {$col}");
            }
        }

        if ($missingOnProd === 0) {
            $this->newLine();
            $this->info('Production has all snapshot columns for shared tables.');
        } else {
            $this->newLine();
            $this->line('Deploy snapshot schema: php artisan db:sync-prod-to-connected --schema-snapshot --deep-clone --force');
        }
    }

    /**
     * @return list<string>
     */
    protected function canonicalTableList(\PDO $src, string $srcSchema, ?\PDO $schemaPdo, string $schemaSchema): array
    {
        if ($this->schemaSnapshotTables !== null) {
            return array_values(array_keys($this->schemaSnapshotTables));
        }
        if ($schemaPdo !== null) {
            return $this->baseTables($schemaPdo, $schemaSchema);
        }

        return $this->baseTables($src, $srcSchema);
    }

    /**
     * @param  array<string, array{columns:list<string>, create_sql:string}>  $snapshotTables
     */
    protected function snapshotSchemaMatchesTarget(array $snapshotTables, \PDO $dst, string $dstSchema): bool
    {
        [, $dstColumns] = $this->schemaSnapshot($dst, $dstSchema);

        foreach ($snapshotTables as $table => $meta) {
            $expected = $meta['columns'] ?? [];
            $actual = $dstColumns[$table] ?? null;
            if ($actual === null || $expected !== $actual) {
                return false;
            }
        }

        return true;
    }

    protected function renderSchemaCompareProdVsNgnClean(\PDO $prod, string $prodSchema, \PDO $ngn, string $ngnSchema): void
    {
        [$prodTables, $prodColumns] = $this->schemaSnapshot($prod, $prodSchema);
        [$ngnTables, $ngnColumns] = $this->schemaSnapshot($ngn, $ngnSchema);

        $onlyNgn = array_values(array_diff($ngnTables, $prodTables));
        $onlyProd = array_values(array_diff($prodTables, $ngnTables));
        $common = array_values(array_intersect($prodTables, $ngnTables));
        sort($common);

        $this->newLine();
        $this->info("Schema diff: production [{$prodSchema}] vs canonical [{$ngnSchema}]");
        $this->line('Tables only in ngn_clean: '.count($onlyNgn));
        $this->line('Tables only in production: '.count($onlyProd));

        $missingOnProd = 0;
        foreach ($common as $table) {
            $missing = array_values(array_diff($ngnColumns[$table] ?? [], $prodColumns[$table] ?? []));
            if ($missing === []) {
                continue;
            }
            $missingOnProd++;
            $this->newLine();
            $this->warn("{$table} — columns on production missing (app expects from ngn_clean):");
            foreach ($missing as $col) {
                $this->line("  - {$col}");
            }
        }

        if ($missingOnProd === 0 && $onlyNgn === []) {
            $this->newLine();
            $this->info('Production has all ngn_clean tables/columns for shared tables.');
        } else {
            $this->newLine();
            $this->line('Fix: php artisan migrate --path=database/migrations/unified-sync --force');
            $this->line('Or sync with: --schema-from-db='.$ngnSchema.' --deep-clone');
        }
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function schemaConfig(string $connection, string $database): ?array
    {
        $cfg = $this->targetConfig($connection);
        if ($cfg === null) {
            return null;
        }
        $cfg['database'] = $database;

        return $cfg;
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
