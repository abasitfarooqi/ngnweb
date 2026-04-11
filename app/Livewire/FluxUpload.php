<?php

namespace App\Livewire;

use App\Jobs\MoveCustomerDocumentToSpacesJob;
use App\Support\CustomerDocumentStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Global Livewire upload component using Flux free components.
 * Use for rental, finance, document upload, or any media collection.
 *
 * Media mode:    modelType, modelId, collection
 * Document mode: documentTypeId, customerId (Spaces when available, else public disk)
 */
class FluxUpload extends Component
{
    use WithFileUploads;

    public array $files = [];

    public ?string $modelType = null;

    public $modelId = null;

    public string $collection = 'uploads';

    public ?int $documentTypeId = null;

    public ?int $customerId = null;

    public ?string $documentNumber = null;

    public ?string $validUntil = null;

    public bool $multiple = true;

    public int $maxFiles = 10;

    public int $maxSizeKb = 51200;

    public string $accept = '';

    public ?string $label = null;

    public ?string $description = null;

    public bool $compact = false;

    public ?string $commitError = null;

    public function mount(
        ?string $modelType = null,
        $modelId = null,
        string $collection = 'uploads',
        ?int $documentTypeId = null,
        ?int $customerId = null,
        ?string $documentNumber = null,
        ?string $validUntil = null,
        ?string $label = null,
        ?string $description = null,
        bool $multiple = true,
        int $maxFiles = 10,
        int $maxSizeKb = 51200,
        string $accept = '',
        bool $compact = false,
    ): void {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->collection = $collection;
        $this->documentTypeId = $documentTypeId;
        $this->customerId = $customerId;
        $this->documentNumber = $documentNumber ?? '';
        $this->validUntil = $validUntil ?? '';
        $this->label = $label;
        $this->description = $description;
        $this->multiple = $multiple;
        $this->maxFiles = $maxFiles;
        $this->maxSizeKb = $maxSizeKb;
        $this->accept = $accept;
        $this->compact = $compact;

        if ($this->documentTypeId && $this->customerId) {
            $this->multiple = false;
            $this->maxFiles = 1;
            $this->maxSizeKb = 10240;
        }
    }

    public function updatedFiles(): void
    {
        if (count($this->files) === 0) {
            return;
        }
        $this->validate([
            'files' => ['array', 'max:'.$this->maxFiles],
            'files.*' => ['file', 'max:'.$this->maxSizeKb],
        ]);
    }

    public function removeTemp(int $index): void
    {
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files);
        }
    }

    public function commit(): void
    {
        $this->commitError = null;

        if (count($this->files) < 1) {
            $this->commitError = 'No files to save. Select a file above.';

            return;
        }

        $this->validate([
            'files' => ['required', 'array', 'min:1', 'max:'.$this->maxFiles],
            'files.*' => ['max:'.$this->maxSizeKb],
        ], ['files.required' => 'Select at least one file to save.']);

        try {
            if ($this->documentTypeId && $this->customerId) {
                $this->commitAsDocument();
            } else {
                $this->commitAsMedia();
            }
            $this->files = [];
            $this->dispatch(
                ($this->documentTypeId && $this->customerId)
                    ? 'document-upload-committed'
                    : 'upload-committed'
            );
        } catch (\Throwable $e) {
            $this->commitError = $e->getMessage();
            report($e);
        }
    }

    protected function commitAsDocument(): void
    {
        $file = $this->files[0] ?? null;
        if (! $file) {
            throw new \RuntimeException('No file to upload.');
        }
        $path = 'customer-documents/'.Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        CustomerDocumentStorage::put($path, $file->get());

        $attributes = [
            'customer_id' => $this->customerId,
            'document_type_id' => $this->documentTypeId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_format' => $file->getClientOriginalExtension(),
            'document_number' => $this->documentNumber ?: '',
            'valid_until' => $this->validUntil ?: null,
        ];
        if (Schema::hasColumn('customer_documents', 'status')) {
            $attributes['status'] = 'pending_review';
        }

        $document = \App\Models\CustomerDocument::create($attributes);
        MoveCustomerDocumentToSpacesJob::dispatch($document->id, $path)
            ->delay(now()->addMinutes(10));
    }

    protected function commitAsMedia(): void
    {
        /** @var Model $model */
        $model = ($this->modelType)::query()->findOrFail($this->modelId);

        foreach ($this->files as $file) {
            $safeName = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $mediaName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $model
                ->addMedia($file->getRealPath())
                ->usingFileName($safeName)
                ->usingName($mediaName)
                ->toMediaCollection($this->collection);
        }
    }

    public function render()
    {
        return view('livewire.flux-upload');
    }
}
