<?php

namespace App\Console\Commands;

use App\Support\ProductionDatabaseRelationAligner;
use Illuminate\Console\Command;

class AlignDatabaseRelationsFromProductionCommand extends Command
{
    protected $signature = 'db:align-relations-from-production
                            {--compare-only : Report production vs connected FK diff without changes}
                            {--relations-only : FK constraints only — no row inserts or updates (matches older production ERD)}
                            {--dry-run : Same as --compare-only}';

    protected $description = 'Align connected DB foreign keys to older production ERD (sync_prod / JSON snapshot).';

    public function handle(): int
    {
        $compare = ProductionDatabaseRelationAligner::compareConnectedToProduction();

        $this->line('');
        $this->info('Production vs connected database relations');
        $this->line('Connected: '.$compare['connected_database']);
        $this->line('Production source: '.($compare['production_database'] ?? 'snapshot/bootstrap'));
        $this->line('Production FK count: '.$compare['production_fk_count']);
        $this->line('Connected FK count: '.$compare['connected_fk_count']);
        $this->line('Missing on connected: '.count($compare['missing_on_connected']));
        $this->line('Extra on connected (new tables): '.count($compare['extra_on_connected']));
        $this->line('Orphan constraints: '.count($compare['orphan_constraints']));
        $this->line('Rule/name mismatches vs production: '.($compare['rule_mismatches'] ?? 0));
        $this->line('');

        if ($compare['missing_on_connected'] !== []) {
            $this->warn('Missing FK constraints on connected DB:');
            foreach (array_slice($compare['missing_on_connected'], 0, 30) as $name) {
                $this->line('  - '.$name);
            }
            if (count($compare['missing_on_connected']) > 30) {
                $this->line('  ... and '.(count($compare['missing_on_connected']) - 30).' more');
            }
            $this->line('');
        }

        if ($compare['orphan_constraints'] !== []) {
            $this->warn('Broken references on connected DB (FK will be skipped until data is fixed manually):');
            foreach ($compare['orphan_constraints'] as $row) {
                $this->line(sprintf('  - %s (%d orphan rows)', $row['constraint'], $row['orphans']));
            }
            $this->line('');
        }

        if ($this->option('compare-only') || $this->option('dry-run')) {
            return self::SUCCESS;
        }

        $relationsOnly = $this->option('relations-only');

        $prompt = $relationsOnly
            ? 'Add/realign FK constraints only (no row data changes)?'
            : 'Repair missing parent rows AND add/realign FK constraints?';

        if (! $this->confirm($prompt, true)) {
            $this->warn('Aborted.');

            return self::FAILURE;
        }

        try {
            $result = $relationsOnly
                ? ProductionDatabaseRelationAligner::alignRelationsOnly()
                : ProductionDatabaseRelationAligner::align();
        } catch (\Throwable $e) {
            $this->error('Alignment failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Catalog source: '.$result['catalog_source'].' ('.$result['catalog_count'].' FK definitions)');

        if (! $relationsOnly) {
            $this->info('Parent rows inserted: '.$result['repair']['inserted']);
        }

        $this->info('FK constraints added: '.count($result['foreign_keys']['added']));
        $this->info('FK constraints realigned: '.count($result['realign']['realigned'] ?? []));
        $this->info('Rule mismatches remaining: '.($result['rule_mismatches_after'] ?? 0));
        $this->info('Orphans remaining: '.$result['local_orphans_after']);

        if (($result['skipped_orphans'] ?? []) !== []) {
            $this->warn('FK skipped due to orphan rows: '.count($result['skipped_orphans']));
        }

        return ($result['rule_mismatches_after'] ?? 0) > 0 ? self::FAILURE : self::SUCCESS;
    }
}
