<?php

namespace App\Jobs;

use App\Models\CustomerDocument;
use App\Support\CustomerDocumentStorage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MoveCustomerDocumentToSpacesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $documentId,
        public string $expectedPath
    ) {}

    public function handle(): void
    {
        $document = CustomerDocument::query()->find($this->documentId);
        if (! $document) {
            return;
        }

        // If user replaced file before this job ran, do nothing.
        if ((string) $document->file_path !== $this->expectedPath) {
            return;
        }

        CustomerDocumentStorage::moveToSpacesAndDeleteLocalIfSynced($this->expectedPath);
    }
}
