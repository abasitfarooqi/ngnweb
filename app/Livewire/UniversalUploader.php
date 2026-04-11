<?php

namespace App\Livewire;

use App\Jobs\MoveCustomerDocumentToSpacesJob;
use App\Models\CustomerDocument;
use App\Support\CustomerDocumentStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class UniversalUploader extends Component
{
    use WithFileUploads;

    /** Single-file mode for document uploads */
    public $file = null;

    /**
     * Livewire bound files (temporary uploads)
     *
     * @var TemporaryUploadedFile[]|array
     */
    public $files = [];

    public ?string $modelType = null;

    public $modelId = null;

    public string $collection = 'uploads';

    /** Document mode: when set, commit stores file (Spaces if available, else public) and creates CustomerDocument */
    public ?int $documentTypeId = null;

    public ?int $customerId = null;

    public ?string $documentNumber = null;

    public ?string $validUntil = null;

    public bool $multiple = true;

    public int $maxFiles = 10;

    public int $maxSizeKb = 51200; // 50 MB

    public string $accept = '';

    public ?string $commitError = null;

    public ?string $commitSuccess = null;

    public function mount(
        ?string $modelType = null,
        $modelId = null,
        string $collection = 'uploads',
        ?int $documentTypeId = null,
        ?int $customerId = null,
        ?string $documentNumber = null,
        ?string $validUntil = null,
    ): void {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->collection = $collection;
        $this->documentTypeId = $documentTypeId;
        $this->customerId = $customerId;
        $this->documentNumber = $documentNumber;
        $this->validUntil = $validUntil;

        if ($this->documentTypeId && $this->customerId) {
            $this->multiple = false;
            $this->maxFiles = 1;
            $this->maxSizeKb = 10240;
        }
    }

    public function updatedFiles(): void
    {
        \Log::info('UniversalUploader updatedFiles()', ['count' => is_array($this->files) ? count($this->files) : 0]);
        if (! is_array($this->files) || count($this->files) === 0) {
            return;
        }
        $this->validate([
            'files' => ['array', 'max:'.$this->maxFiles],
            'files.*' => ['file', 'max:'.$this->maxSizeKb],
        ]);
    }

    public function updatedFile(): void
    {
        \Log::info('UniversalUploader updatedFile()', ['has_file' => $this->file instanceof TemporaryUploadedFile]);

        if (! ($this->file instanceof TemporaryUploadedFile)) {
            return;
        }

        $this->validate([
            'file' => ['file', 'max:'.$this->maxSizeKb],
        ]);
    }

    public function removeTemp(int $index): void
    {
        if (! is_array($this->files)) {
            $this->files = [];

            return;
        }
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files);
        }
    }

    public function testButton(): void
    {
        \Log::info('TEST BUTTON CLICKED!');
        $this->commitError = 'Test button works! Livewire is functional.';
    }

    public function debugUpload(): void
    {
        \Log::info('debugUpload called', [
            'files' => $this->files,
            'files_count' => count($this->files),
            'files_is_array' => is_array($this->files),
        ]);
        $this->commitError = 'Debug: Files count = '.count($this->files).', Type = '.gettype($this->files);
    }

    public function commit(): void
    {
        \Log::info('UniversalUploader::commit()', [
            'files_count' => is_array($this->files) ? count($this->files) : 0,
            'files_type' => gettype($this->files),
            'has_file' => $this->file instanceof TemporaryUploadedFile,
            'documentMode' => (bool) ($this->documentTypeId && $this->customerId),
        ]);

        $this->commitError = null;
        $this->commitSuccess = null;

        try {
            if ($this->documentTypeId && $this->customerId) {
                if (! ($this->file instanceof TemporaryUploadedFile)) {
                    $this->commitError = 'Please choose a file first.';
                    $this->dispatch('document-upload-failed', message: $this->commitError);

                    return;
                }
                $this->validate([
                    'file' => ['required', 'file', 'max:'.$this->maxSizeKb],
                ], ['file.required' => 'Please choose a file first.']);
                $this->commitAsDocument($this->file);
                $this->file = null;
                $this->commitSuccess = 'Upload completed successfully.';
            } else {
                $count = is_array($this->files) ? count($this->files) : 0;
                if ($count < 1) {
                    $this->commitError = 'No files to save. Select a file above and wait for it to appear in staged files.';
                    $this->dispatch('document-upload-failed', message: $this->commitError);

                    return;
                }
                $this->validate([
                    'files' => ['required', 'array', 'min:1', 'max:'.$this->maxFiles],
                    'files.*' => ['file', 'max:'.$this->maxSizeKb],
                ], ['files.required' => 'Select at least one file to save.']);
                $this->commitAsMedia();
                $this->files = [];
                $this->commitSuccess = 'Upload completed successfully.';
            }
            $this->dispatch(
                ($this->documentTypeId && $this->customerId)
                    ? 'document-upload-committed'
                    : 'upload-committed'
            );
        } catch (\Throwable $e) {
            $this->commitError = $e->getMessage();
            \Log::error('UniversalUploader::commit failed', ['message' => $e->getMessage()]);
            $this->dispatch('document-upload-failed', message: $e->getMessage());
            report($e);
        }
    }

    protected function commitAsDocument(TemporaryUploadedFile $file): void
    {
        $path = 'customer-documents/'.Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        CustomerDocumentStorage::put($path, $file->get());
        \Log::info('UniversalUploader: customer document stored', ['path' => $path]);

        $existing = CustomerDocument::query()->where([
            'customer_id' => $this->customerId,
            'document_type_id' => $this->documentTypeId,
        ])->first();

        $oldPath = $existing?->file_path;

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

        $savedDocument = CustomerDocument::updateOrCreate([
            'customer_id' => $this->customerId,
            'document_type_id' => $this->documentTypeId,
        ], $attributes);

        if ($oldPath && $oldPath !== $path) {
            CustomerDocumentStorage::delete($oldPath);
        }

        MoveCustomerDocumentToSpacesJob::dispatch($savedDocument->id, $path)
            ->delay(now()->addMinutes(10));

        $this->dispatch('document-upload-committed',
            documentTypeId: $this->documentTypeId,
            fileName: $savedDocument->file_name,
            uploadedAt: now()->toIso8601String(),
            storageTarget: CustomerDocumentStorage::spacesConfigured() ? 'site-storage -> queued to digitalocean-spaces' : 'site-storage'
        );
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
        return view('livewire.universal-uploader');
    }
}
