<?php

namespace App\Console\Commands;

use App\Support\CloudwaysToDigitalOceanDataMigrator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CloudwaysToDigitalOceanDataMigratorCommand extends Command
{
    protected $signature = 'cloudways-to-digital-ocean:sync-data
                            {--only= : Comma-separated tables to overwrite only (skips full DB sync)}
                            {--skip-migrate : Skip CREATE DATABASE + migrate (fast partial overwrite)}';

    protected $description = 'Create target DB, migrate schema, overwrite row data from production (Cloudways → connected DB). Use --only=club_members for a quick club-only overwrite.';

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
            $this->error('Source and target are the same database. Refusing to overwrite production.');

            return self::FAILURE;
        }

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
