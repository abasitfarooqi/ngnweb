<?php

namespace App\Jobs;

use App\Support\AgreementContractStorage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ArchiveAgreementPdfToSpacesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 12;

    /** @return list<int> */
    public function backoff(): array
    {
        return [120, 180, 300, 600, 900, 1200, 1800, 3600, 3600, 7200, 7200, 7200];
    }

    public function __construct(
        public string $modelClass,
        public int $recordId,
        public string $expectedPath,
    ) {}

    public function handle(): void
    {
        $archived = AgreementContractStorage::archiveRecord(
            $this->modelClass,
            $this->recordId,
            $this->expectedPath,
        );

        if ($archived) {
            return;
        }

        if ($this->attempts() >= $this->tries) {
            return;
        }

        // PDF may not exist yet (record created before file write) or worker ran early.
        $this->release($this->backoff()[min($this->attempts() - 1, count($this->backoff()) - 1)]);
    }
}
