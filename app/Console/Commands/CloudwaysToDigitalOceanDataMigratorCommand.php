<?php

namespace App\Console\Commands;

use App\Support\CloudwaysToDigitalOceanDataMigrator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CloudwaysToDigitalOceanDataMigratorCommand extends Command
{
    protected $signature = 'cloudways-to-digital-ocean:sync-data
                            {--only= : Comma-separated tables to overwrite only (skips full DB sync)}
                            {--skip-migrate : Skip CREATE DATABASE + migrate (fast partial overwrite)}
                            {--day= : YYYY-MM-DD insert-only day merge from production (no truncate/update/delete)}
                            {--dry-run : With --day, count candidates and PK conflicts only (no business writes)}';

    protected $description = 'Create target DB, migrate schema, overwrite row data from production (Cloudways → connected DB). Use --day=YYYY-MM-DD for insert-only day merge.';

    public function handle(): int
    {
        $source = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
        if ($source === null) {
            $this->error('Missing production credentials. Set SYNC_PROD_DB_* in .env');

            return self::FAILURE;
        }

        $connection = (string) config('database.default', 'mysql');
        $target = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel($connection);
        if ($target === null) {
            $this->error("Invalid target connection [{$connection}]. Check DB_* in .env");

            return self::FAILURE;
        }

        if (CloudwaysToDigitalOceanDataMigrator::isSameDatabase($source, $target)) {
            $this->error('Source and target are the same database. Refusing to sync.');

            return self::FAILURE;
        }

        $dayRaw = trim((string) $this->option('day'));
        if ($dayRaw !== '') {
            return $this->handleDayMerge($source, $target, $dayRaw);
        }

        return $this->handleFullOverwrite($source, $target);
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $source
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $target
     */
    private function handleDayMerge(array $source, array $target, string $dayRaw): int
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayRaw)) {
            $this->error('Invalid --day value. Use YYYY-MM-DD (e.g. 2026-07-14).');

            return self::FAILURE;
        }

        $dayStart = $dayRaw.' 00:00:00';
        $dayEndExclusive = date('Y-m-d', strtotime($dayRaw.' +1 day')).' 00:00:00';
        $dryRun = (bool) $this->option('dry-run');
        $tables = CloudwaysToDigitalOceanDataMigrator::DAY_MERGE_TABLE_ORDER;

        $this->line('');
        $this->info($dryRun
            ? 'Cloudways → DigitalOcean day merge (DRY RUN — no business writes)'
            : 'Cloudways → DigitalOcean day merge (insert-only, no overwrite)');
        $this->line('DAY WINDOW: ['.$dayStart.', '.$dayEndExclusive.')');
        $this->line('SOURCE (production, read-only): '.$source['host'].':'.$source['port'].'/'.$source['database']);
        $this->line('TARGET (current DB, insert missing only): '.$target['host'].':'.$target['port'].'/'.$target['database']);
        $this->line('TABLES: '.implode(', ', $tables));
        $this->line('');

        try {
            $migrator = new CloudwaysToDigitalOceanDataMigrator($source, $target);
            $bar = $this->output->createProgressBar(count($tables));
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $bar->setMessage('starting');
            $bar->start();

            $result = $migrator->mergeDayWindow(
                $tables,
                $dayStart,
                $dayEndExclusive,
                $dryRun,
                function (string $table, array $entry) use ($bar): void {
                    $bar->setMessage(sprintf(
                        '%s c=%d i=%d x=%d',
                        $table,
                        (int) ($entry['candidates'] ?? 0),
                        (int) ($entry['inserted'] ?? 0),
                        (int) ($entry['conflicts'] ?? 0)
                    ));
                    $bar->advance();
                }
            );

            $bar->setMessage('done');
            $bar->finish();
            $this->newLine(2);

            foreach ($result['errors'] as $error) {
                $this->warn(sprintf(
                    'FAIL %s [%s]: %s',
                    $error['table'],
                    $error['phase'] ?? $error['status'],
                    $error['message'] ?? 'unknown error'
                ));
            }

            $this->info('Mode: insert-only day merge'.($dryRun ? ' (dry-run)' : ''));
            $this->info('Merge batch: '.$result['merge_batch']);
            $this->info('Tables total: '.$result['tables_total']);
            $this->info('Tables OK: '.$result['tables_ok']);
            $this->info('Tables skipped: '.$result['tables_skipped']);
            $this->info('Tables failed: '.$result['tables_failed']);
            $this->info(($dryRun ? 'Would insert: ' : 'Rows inserted: ').number_format($result['rows_copied']));
            $this->info(($dryRun ? 'Would conflict: ' : 'Rows conflicted: ').number_format($result['rows_conflicted']));
            $this->info('Report: '.$result['report_path']);

            return $result['tables_failed'] > 0 ? self::FAILURE : self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Day merge aborted: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $source
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $target
     */
    private function handleFullOverwrite(array $source, array $target): int
    {
        $onlyRaw = trim((string) $this->option('only'));
        $onlyTables = $onlyRaw === ''
            ? []
            : array_values(array_filter(array_map(
                static fn (string $t): string => trim($t),
                explode(',', $onlyRaw)
            )));

        $skipMigrate = (bool) $this->option('skip-migrate') || $onlyTables !== [];

        $this->line('');
        $this->info($onlyTables === []
            ? 'Cloudways → DigitalOcean data sync (full)'
            : 'Cloudways → DigitalOcean data sync (tables only: '.implode(', ', $onlyTables).')');
        $this->line('SOURCE (production, read-only): '.$source['host'].':'.$source['port'].'/'.$source['database']);
        $this->line('TARGET (overwrite): '.$target['host'].':'.$target['port'].'/'.$target['database']);
        $this->line('');

        try {
            if (! $skipMigrate) {
                $this->info('Step 1/3: ensure target database exists…');
                CloudwaysToDigitalOceanDataMigrator::ensureDatabaseExists($target);
                $this->info('  Database ready: '.$target['database']);

                $this->info('Step 2/3: migrate schema on target…');
                Artisan::call('migrate', ['--force' => true]);
                $migrateOutput = trim(Artisan::output());
                if ($migrateOutput !== '') {
                    $this->line($migrateOutput);
                }

                $this->info('Step 3/3: copy production data (truncate + insert, preserve PKs)…');
            } else {
                $this->info('Quick mode: truncate + insert selected table(s) only (no migrate).');
            }

            $migrator = new CloudwaysToDigitalOceanDataMigrator($source, $target);
            $tables = $onlyTables === [] ? $migrator->listProductionTables() : $onlyTables;
            $bar = $this->output->createProgressBar(count($tables));
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $bar->setMessage('starting');
            $bar->start();

            $result = $migrator->syncTables($tables, function (string $table, array $entry) use ($bar): void {
                $bar->setMessage($table);
                $bar->advance();
            });

            $bar->setMessage('done');
            $bar->finish();
            $this->newLine(2);

            foreach ($result['errors'] as $error) {
                $this->warn(sprintf(
                    'FAIL %s [%s]: %s',
                    $error['table'],
                    $error['phase'] ?? $error['status'],
                    $error['message'] ?? 'unknown error'
                ));
            }

            $this->info('Tables total: '.$result['tables_total']);
            $this->info('Tables OK: '.$result['tables_ok']);
            $this->info('Tables skipped: '.$result['tables_skipped']);
            $this->info('Tables failed: '.$result['tables_failed']);
            $this->info('Rows copied: '.number_format($result['rows_copied']));
            $this->info('Report: '.$result['report_path']);

            return $result['tables_failed'] > 0 ? self::FAILURE : self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Sync aborted: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
