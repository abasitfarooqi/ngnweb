<?php

namespace App\Console\Commands;

use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Support\AgreementContractStorage;
use Illuminate\Console\Command;

/** Fallback when queue workers miss delayed archive jobs. */
class ArchivePendingAgreementPdfs extends Command
{
    protected $signature = 'agreement:archive-pending-pdfs
                            {--force : Archive immediately, ignore delay window}
                            {--limit=40 : Maximum DB records to process per model per run}
                            {--orphan-limit=20 : Maximum orphan PDF files on disk per run}';

    protected $description = 'Upload signed rental + finance contract PDFs to private DO Spaces after the delay.';

    public function handle(): int
    {
        if (! AgreementContractStorage::spacesConfigured()) {
            $this->warn('DO Spaces is not configured — skipping archive run.');

            return self::SUCCESS;
        }

        $cutoff = now()->subMinutes(AgreementContractStorage::archiveDelayMinutes());
        $limit = max(1, (int) $this->option('limit'));
        $archived = 0;
        $skipped = 0;
        $missing = 0;

        foreach ([CustomerAgreement::class, CustomerContract::class] as $modelClass) {
            $processed = 0;

            $query = $modelClass::query()
                ->where('sent_private', false)
                ->whereNotNull('file_path')
                ->where('file_path', 'not like', AgreementContractStorage::spacesPrefix().'%');

            if (! $this->option('force')) {
                $query->where('created_at', '<=', $cutoff);
            }

            foreach ($query->orderByDesc('created_at')->cursor() as $record) {
                if ($processed >= $limit) {
                    break;
                }

                $path = AgreementContractStorage::normalizePath($record->file_path);

                if ($path === '' || ! AgreementContractStorage::isContractPdfPath($path)) {
                    $skipped++;

                    continue;
                }

                if (! AgreementContractStorage::hasLocalSource($path)) {
                    $missing++;

                    continue;
                }

                $processed++;

                if (AgreementContractStorage::archiveRecord($modelClass, (int) $record->id, $path)) {
                    $archived++;
                    $this->line('Archived '.class_basename($modelClass)." #{$record->id}");
                }
            }
        }

        $orphanLimit = max(0, (int) $this->option('orphan-limit'));
        $orphans = $orphanLimit > 0
            ? AgreementContractStorage::archiveOrphanContractPdfs($orphanLimit, (bool) $this->option('force'))
            : 0;

        $this->info("Done. Archived {$archived} DB record(s), {$orphans} orphan file(s), skipped {$skipped}, missing source {$missing}.");

        return self::SUCCESS;
    }
}
