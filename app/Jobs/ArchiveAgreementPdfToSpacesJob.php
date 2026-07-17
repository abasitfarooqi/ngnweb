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

    public function __construct(
        public string $modelClass,
        public int $recordId,
        public string $expectedPath,
    ) {}

    public function handle(): void
    {
        AgreementContractStorage::archiveRecord(
            $this->modelClass,
            $this->recordId,
            $this->expectedPath,
        );
    }
}
