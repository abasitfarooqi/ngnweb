<?php

namespace App\Console\Commands;

use App\Support\NgnDbSyncToolkit;
use App\Support\ProdToLocalTableSync;
use Illuminate\Console\Command;

class NgnDbSyncCommand extends Command
{
    protected $signature = 'db:ngn-sync
                            {action : inspect|generate|sync-production|export-local-snapshot|apply-local-snapshot}
                            {--connection= : Target Laravel connection for writes and local-host config (default: database.default)}
                            {--local-db= : Local source database name used as ngn_clean baseline (default: ngn_clean)}
                            {--report-folder=database/schema/ngn-sync : Comparison output folder}
                            {--bootstrap-folder=database/migrations/ngn-sync/bootstrap : Full merged-schema migrations}
                            {--align-folder=database/migrations/ngn-sync/production-align : Production alignment migrations}
                            {--snapshot-folder=database/seeders/data/ngn-local-snapshot : Local snapshot folder}
                            {--prefer-case= : local or production when names differ only by case}
                            {--with-row-counts : Include per-table row counts in the comparison report}
                            {--with-local-snapshot : After production sync, also replay the local snapshot for local-only tables}
                            {--force : Allow destructive overwrite actions on the target DB}';

    protected $description = 'Inspect production vs local ngn_clean, generate merged migrations, sync production data, and export/replay local snapshots.';

    public function handle(): int
    {
        $action = (string) $this->argument('action');

        return match ($action) {
            'inspect' => $this->inspectOnly(),
            'generate' => $this->generateArtifacts(),
            'sync-production' => $this->syncProduction(),
            'export-local-snapshot' => $this->exportLocalSnapshot(),
            'apply-local-snapshot' => $this->applyLocalSnapshot(),
            default => $this->invalidAction($action),
        };
    }

    protected function inspectOnly(): int
    {
        [$productionSchema, $localSchema, $comparison] = $this->inspectSchemas();
        if ($comparison === null) {
            return 1;
        }

        $report = NgnDbSyncToolkit::writeComparisonReport($comparison, $this->reportFolder());
        $this->printComparisonSummary($comparison, $report['json'], $report['markdown']);

        return 0;
    }

    protected function generateArtifacts(): int
    {
        [$productionSchema, $localSchema, $comparison] = $this->inspectSchemas();
        if ($comparison === null || $productionSchema === null || $localSchema === null) {
            return 1;
        }

        $report = NgnDbSyncToolkit::writeComparisonReport($comparison, $this->reportFolder());
        $this->printComparisonSummary($comparison, $report['json'], $report['markdown']);

        try {
            $plan = NgnDbSyncToolkit::buildUnifiedPlan(
                $productionSchema,
                $localSchema,
                $comparison,
                $this->preferCase()
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $files = NgnDbSyncToolkit::writeMigrationArtifacts(
            $comparison,
            $plan,
            $localSchema,
            $this->bootstrapFolder(),
            $this->alignFolder()
        );

        $this->newLine();
        $this->info('Migration artifacts generated.');
        $this->line('Bootstrap migrations: '.count($files['bootstrap']));
        $this->line('Production alignment migrations: '.count($files['alignment']));
        $this->line('Bootstrap folder: '.$this->bootstrapFolder());
        $this->line('Align folder: '.$this->alignFolder());
        $this->line('Blank DB bootstrap: php artisan migrate --path='.trim(str_replace(base_path(), '', $this->bootstrapFolder()), '/'));
        $this->line('Production schema align: php artisan migrate --path='.trim(str_replace(base_path(), '', $this->alignFolder()), '/').' --force');

        return 0;
    }

    protected function syncProduction(): int
    {
        if (! $this->option('force')) {
            $this->error('sync-production is destructive. Re-run with --force.');

            return 1;
        }

        [$productionSchema, $localSchema, $comparison, $productionConfig, $targetConfig] = $this->inspectSchemas(withConfigs: true);
        if ($comparison === null || $productionSchema === null || $localSchema === null || $productionConfig === null || $targetConfig === null) {
            return 1;
        }

        $report = NgnDbSyncToolkit::writeComparisonReport($comparison, $this->reportFolder());
        $this->printComparisonSummary($comparison, $report['json'], $report['markdown']);

        if ($this->sameDatabase($productionConfig, $targetConfig)) {
            $this->error('Target database resolves to production. Refusing to overwrite.');

            return 1;
        }

        try {
            $plan = NgnDbSyncToolkit::buildUnifiedPlan(
                $productionSchema,
                $localSchema,
                $comparison,
                $this->preferCase()
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }

        try {
            $production = ProdToLocalTableSync::connectSource($productionConfig);
            $target = ProdToLocalTableSync::connectTarget($targetConfig);
            $result = NgnDbSyncToolkit::syncProductionIntoTarget($production, $target, $plan, $comparison);
        } catch (\Throwable $e) {
            $this->error('Production sync failed: '.$e->getMessage());

            return 1;
        }

        $this->newLine();
        $this->info('Production sync completed.');
        $this->line('Tables recreated: '.$result['tables']);
        $this->line('Rows copied: '.$result['rows']);

        if ($this->option('with-local-snapshot')) {
            $snapshotFolder = $this->snapshotFolder();
            if (! is_file($snapshotFolder.'/manifest.json')) {
                $this->warn('Snapshot manifest not found, so local snapshot replay was skipped: '.$snapshotFolder);

                return 0;
            }

            try {
                $target = ProdToLocalTableSync::connectTarget($targetConfig);
                $snapshot = NgnDbSyncToolkit::applySnapshot($target, $snapshotFolder);
            } catch (\Throwable $e) {
                $this->error('Local snapshot replay failed: '.$e->getMessage());

                return 1;
            }

            $this->line('Local snapshot tables replayed: '.$snapshot['tables']);
            $this->line('Local snapshot rows replayed: '.$snapshot['rows']);
        }

        return 0;
    }

    protected function exportLocalSnapshot(): int
    {
        $localConfig = $this->localSourceConfig();
        if ($localConfig === null) {
            $this->error('Local source DB config is invalid.');

            return 1;
        }

        try {
            $source = ProdToLocalTableSync::connectSource($localConfig);
            $result = NgnDbSyncToolkit::exportSnapshot($source, $localConfig['database'], $this->snapshotFolder());
        } catch (\Throwable $e) {
            $this->error('Snapshot export failed: '.$e->getMessage());

            return 1;
        }

        $this->info('Local snapshot exported.');
        $this->line('Tables exported: '.$result['tables']);
        $this->line('Rows exported: '.$result['rows']);
        $this->line('Folder: '.$this->snapshotFolder());
        $this->line('Replay later: php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder');

        return 0;
    }

    protected function applyLocalSnapshot(): int
    {
        if (! $this->option('force')) {
            $this->error('apply-local-snapshot is destructive. Re-run with --force.');

            return 1;
        }

        $targetConfig = $this->targetConfig();
        if ($targetConfig === null) {
            $this->error('Target DB config is invalid.');

            return 1;
        }

        try {
            $target = ProdToLocalTableSync::connectTarget($targetConfig);
            $result = NgnDbSyncToolkit::applySnapshot($target, $this->snapshotFolder());
        } catch (\Throwable $e) {
            $this->error('Snapshot replay failed: '.$e->getMessage());

            return 1;
        }

        $this->info('Local snapshot applied.');
        $this->line('Tables replayed: '.$result['tables']);
        $this->line('Rows replayed: '.$result['rows']);

        return 0;
    }

    /**
     * @return array{0:array<string,mixed>|null,1:array<string,mixed>|null,2:array<string,mixed>|null,3:array<string,string|int>|null,4:array<string,string|int>|null}
     */
    protected function inspectSchemas(bool $withConfigs = false): array
    {
        $productionConfig = $this->productionConfig();
        if ($productionConfig === null) {
            $this->error('Missing production credentials in SYNC_PROD_DB_*.');

            return [null, null, null, null, null];
        }

        $localConfig = $this->localSourceConfig();
        if ($localConfig === null) {
            $this->error('Local source DB config is invalid.');

            return [null, null, null, null, null];
        }

        try {
            $production = ProdToLocalTableSync::connectSource($productionConfig);
            $local = ProdToLocalTableSync::connectSource($localConfig);
        } catch (\Throwable $e) {
            $this->error('DB connection failed: '.$e->getMessage());

            return [null, null, null, null, null];
        }

        $this->info('Production DB: '.$productionConfig['host'].':'.$productionConfig['port'].'/'.$productionConfig['database']);
        $this->info('Local source DB: '.$localConfig['host'].':'.$localConfig['port'].'/'.$localConfig['database']);
        if ($withConfigs) {
            $targetConfig = $this->targetConfig();
            if ($targetConfig !== null) {
                $this->info('Target DB: '.$targetConfig['host'].':'.$targetConfig['port'].'/'.$targetConfig['database']);
            }
        }

        try {
            $productionSchema = NgnDbSyncToolkit::inspectSchema($production, $productionConfig['database'], (bool) $this->option('with-row-counts'));
            $localSchema = NgnDbSyncToolkit::inspectSchema($local, $localConfig['database'], (bool) $this->option('with-row-counts'));
            $localBeforeSupplements = $localSchema;
            $localSchema = NgnDbSyncToolkit::applyLocalSchemaSupplements($localSchema);
            $supplementedTables = [];
            foreach ($localSchema['tables'] ?? [] as $tableName => $tableMeta) {
                $beforeColumns = $localBeforeSupplements['tables'][$tableName]['columns'] ?? [];
                $afterColumns = $tableMeta['columns'] ?? [];
                $addedColumns = array_values(array_diff($afterColumns, $beforeColumns));
                if ($addedColumns !== []) {
                    $supplementedTables[$tableName] = $addedColumns;
                }
            }
            if ($supplementedTables !== []) {
                $parts = [];
                foreach ($supplementedTables as $tableName => $columns) {
                    $parts[] = $tableName.': '.implode(', ', $columns);
                }
                $this->warn('Local schema supplements applied: '.implode(' | ', $parts));
            }
            $comparison = NgnDbSyncToolkit::compareSchemas($productionSchema, $localSchema);
        } catch (\Throwable $e) {
            $this->error('Schema inspection failed: '.$e->getMessage());

            return [null, null, null, null, null];
        }

        return [
            $productionSchema,
            $localSchema,
            $comparison,
            $withConfigs ? $productionConfig : null,
            $withConfigs ? $this->targetConfig() : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $comparison
     */
    protected function printComparisonSummary(array $comparison, string $jsonPath, string $markdownPath): void
    {
        $summary = $comparison['summary'];

        $this->newLine();
        $this->info('Comparison report written.');
        $this->line('Tables only in production: '.$summary['tables_only_production']);
        $this->line('Tables only in local: '.$summary['tables_only_local']);
        $this->line('Shared tables: '.$summary['tables_shared']);
        $this->line('Table case conflicts: '.$summary['table_case_conflicts']);
        $this->line('Column case conflicts: '.$summary['column_case_conflicts']);
        $this->line('Sync blocker tables: '.$summary['sync_blocker_tables']);
        $this->line('JSON: '.$jsonPath);
        $this->line('Markdown: '.$markdownPath);
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function targetConfig(): ?array
    {
        return $this->connectionConfig($this->connectionName());
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function localSourceConfig(): ?array
    {
        $config = $this->connectionConfig($this->connectionName());
        if ($config === null) {
            return null;
        }

        $config['database'] = $this->localDatabaseName();

        return $config;
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
     * @param  array<string,string|int>  $a
     * @param  array<string,string|int>  $b
     */
    protected function sameDatabase(array $a, array $b): bool
    {
        return strtolower((string) $a['host']) === strtolower((string) $b['host'])
            && (int) $a['port'] === (int) $b['port']
            && strtolower((string) $a['database']) === strtolower((string) $b['database']);
    }

    protected function connectionName(): string
    {
        return (string) ($this->option('connection') ?: config('database.default', 'mysql'));
    }

    protected function localDatabaseName(): string
    {
        return (string) ($this->option('local-db') ?: env('NGN_CLEAN_SEED_SOURCE_DB', 'ngn_clean'));
    }

    protected function reportFolder(): string
    {
        return $this->absolutePath((string) $this->option('report-folder'));
    }

    protected function bootstrapFolder(): string
    {
        return $this->absolutePath((string) $this->option('bootstrap-folder'));
    }

    protected function alignFolder(): string
    {
        return $this->absolutePath((string) $this->option('align-folder'));
    }

    protected function snapshotFolder(): string
    {
        return $this->absolutePath((string) $this->option('snapshot-folder'));
    }

    protected function preferCase(): ?string
    {
        $value = trim((string) $this->option('prefer-case'));

        return in_array($value, ['local', 'production'], true) ? $value : null;
    }

    protected function absolutePath(string $path): string
    {
        $trimmed = trim($path);

        return str_starts_with($trimmed, '/') ? $trimmed : base_path(trim($trimmed, '/'));
    }

    protected function invalidAction(string $action): int
    {
        $this->error('Unknown action: '.$action);
        $this->line('Allowed actions: inspect, generate, sync-production, export-local-snapshot, apply-local-snapshot');

        return 1;
    }
}
