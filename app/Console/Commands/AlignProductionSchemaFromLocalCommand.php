<?php

namespace App\Console\Commands;

use App\Support\CloudwaysToDigitalOceanDataMigrator;
use App\Support\ProductionSchemaAligner;
use Illuminate\Console\Command;

class AlignProductionSchemaFromLocalCommand extends Command
{
    protected $signature = 'production:align-schema-from-local
                            {--execute : Apply CREATE TABLE / ADD COLUMN on production (default is dry-run only)}
                            {--confirm= : Must exactly match SYNC_PROD_DB_DATABASE to run with --execute}';

    protected $description = 'Add missing tables and columns to Cloudways production from connected DB schema. Never deletes or updates row data.';

    public function handle(): int
    {
        $production = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
        if ($production === null) {
            $this->error('Missing production credentials. Set SYNC_PROD_DB_* in .env');

            return self::FAILURE;
        }

        $connection = (string) config('database.default', 'mysql');
        $reference = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel($connection);
        if ($reference === null) {
            $this->error("Invalid reference connection [{$connection}]. Check DB_* in .env");

            return self::FAILURE;
        }

        if (CloudwaysToDigitalOceanDataMigrator::isSameDatabase($production, $reference)) {
            $this->error('Reference and production are the same database. Refusing.');

            return self::FAILURE;
        }

        $execute = (bool) $this->option('execute');
        $confirm = trim((string) $this->option('confirm'));

        $this->line('');
        $this->info('Production schema align (ADD ONLY — no row data changes)');
        $this->line('REFERENCE (new schema, read only): '.$reference['host'].':'.$reference['port'].'/'.$reference['database']);
        $this->line('PRODUCTION (DDL target): '.$production['host'].':'.$production['port'].'/'.$production['database']);
        $this->line('MODE: '.($execute ? 'EXECUTE' : 'DRY RUN (preview only)'));
        $this->line('');

        if ($execute) {
            if ($confirm !== $production['database']) {
                $this->error('Refusing to execute. Pass --confirm='.$production['database'].' exactly.');

                return self::FAILURE;
            }

            if (! $this->confirm('This will ALTER production schema (CREATE TABLE / ADD COLUMN only). Continue?', false)) {
                $this->warn('Aborted.');

                return self::FAILURE;
            }
        }

        try {
            $aligner = new ProductionSchemaAligner($production, $reference);

            if (! $execute) {
                $plan = $aligner->buildPlan();
                $this->printPlanSummary($plan);

                $result = $aligner->apply(true, function (string $type, array $entry): void {
                    $this->printActionLine($type, $entry, true);
                });
                $this->newLine();
                $this->info('Dry-run complete. No changes were made on production.');
                $this->info('Report: '.$result['report_path']);
                $this->line('');
                $this->line('To apply on production:');
                $this->line('  php artisan production:align-schema-from-local --execute --confirm='.$production['database']);

                return self::SUCCESS;
            }

            $plan = $aligner->buildPlan();
            $actionCount = count($plan['actions']);

            $bar = $this->output->createProgressBar($actionCount);
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $bar->setMessage('starting');
            if ($actionCount === 0) {
                $this->warn('No schema changes required — production already matches reference.');
            } else {
                $bar->start();
            }

            $result = $aligner->apply(false, function (string $type, array $entry) use ($bar, $actionCount): void {
                if ($actionCount > 0) {
                    $label = ($entry['table'] ?? '?').($type === 'add_column' ? '.'.($entry['column'] ?? '?') : '');
                    $bar->setMessage($label);
                    $bar->advance();
                }
                $this->printActionLine($type, $entry, false);
            });

            if ($actionCount > 0) {
                $bar->setMessage('done');
                $bar->finish();
            }
            $this->newLine(2);

            foreach ($result['entries'] as $entry) {
                $status = (string) ($entry['status'] ?? '');
                if (! in_array($status, ['failed', 'blocked'], true)) {
                    continue;
                }
                $target = ($entry['table'] ?? '?').(isset($entry['column']) ? '.'.$entry['column'] : '');
                $this->warn(sprintf(
                    '%s %s: %s',
                    strtoupper($status),
                    $target,
                    $entry['message'] ?? 'unknown'
                ));
            }

            $summary = $result['summary'];
            $this->info('Tables created: '.$summary['tables_created']);
            $this->info('Columns added: '.$summary['columns_added']);
            $this->info('Skipped: '.$summary['skipped']);
            $this->info('Blocked (unsafe): '.$summary['blocked']);
            $this->info('Failed: '.$summary['failed']);
            $this->info('Report: '.$result['report_path']);

            return ($summary['failed'] ?? 0) > 0 ? self::FAILURE : self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Schema align aborted: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    protected function printPlanSummary(array $plan): void
    {
        $summary = $plan['summary'] ?? [];
        $this->info('Schema comparison');
        $this->line('Tables only on reference (will CREATE): '.($summary['tables_only_ngn_clean'] ?? 0));
        $this->line('Tables only on production (left untouched): '.($summary['tables_only_production'] ?? 0));
        $this->line('Shared tables: '.($summary['tables_shared'] ?? 0));
        $this->line('Tables needing column adds on production: '.($summary['tables_with_column_gaps'] ?? 0));

        $planned = 0;
        $blocked = 0;
        foreach ($plan['actions'] as $action) {
            $status = (string) ($action['status'] ?? '');
            if ($status === 'planned') {
                $planned++;
            }
            if ($status === 'blocked') {
                $blocked++;
            }
        }

        $this->newLine();
        $this->line('Actions planned: '.$planned);
        if ($blocked > 0) {
            $this->warn('Actions blocked (unsafe on populated tables): '.$blocked);
        }
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    protected function printActionLine(string $type, array $entry, bool $dryRun): void
    {
        $status = strtoupper((string) ($entry['status'] ?? 'unknown'));
        $target = (string) ($entry['table'] ?? '?');
        if ($type === 'add_column') {
            $target .= '.'.($entry['column'] ?? '?');
        }

        $prefix = $dryRun ? '[DRY RUN]' : '[LIVE]';
        $message = (string) ($entry['message'] ?? '');

        if (in_array($entry['status'] ?? '', ['failed', 'blocked'], true)) {
            $this->warn("  {$prefix} {$status} {$target} — {$message}");
        } elseif (($entry['status'] ?? '') === 'skipped') {
            $this->line("  {$prefix} {$status} {$target} — {$message}");
        } else {
            $this->info("  {$prefix} {$status} {$target} — {$message}");
        }

        if (! empty($entry['sql']) && in_array($entry['status'] ?? '', ['dry_run', 'ok', 'planned'], true)) {
            $sql = preg_replace('/\s+/', ' ', trim((string) $entry['sql']));
            if (strlen($sql) > 140) {
                $sql = substr($sql, 0, 137).'...';
            }
            $this->line('           SQL: '.$sql);
        }
    }
}
