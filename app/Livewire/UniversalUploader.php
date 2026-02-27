<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UniversalUploader extends Component
{
    use WithFileUploads;

    /**
     * Livewire bound files (temporary uploads)
     * @var TemporaryUploadedFile[]|array
     */
    public $files = [];

    public ?string $modelType  = null;
    public $modelId            = null;
    public string $collection  = 'uploads';

    /** Document mode: when set, commit stores to DO Spaces and creates CustomerDocument */
    public ?int    $documentTypeId  = null;
    public ?int    $customerId      = null;
    public ?string $documentNumber  = null;
    public ?string $validUntil      = null;

    public bool   $multiple   = true;
    public int    $maxFiles   = 10;
    public int    $maxSizeKb  = 51200; // 50 MB
    public string $accept     = '';

    public ?string $commitError = null;

    public function mount(
        ?string $modelType      = null,
        $modelId                = null,
        string  $collection     = 'uploads',
        ?int    $documentTypeId = null,
        ?int    $customerId     = null,
        ?string $documentNumber = null,
        ?string $validUntil     = null,
    ): void {
        $this->modelType      = $modelType;
        $this->modelId        = $modelId;
        $this->collection     = $collection;
        $this->documentTypeId = $documentTypeId;
        $this->customerId     = $customerId;
        $this->documentNumber = $documentNumber;
        $this->validUntil     = $validUntil;

        if ($this->documentTypeId && $this->customerId) {
            $this->multiple  = false;
            $this->maxFiles  = 1;
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
            'files'   => ['array', 'max:' . $this->maxFiles],
            'files.*' => ['file', 'max:' . $this->maxSizeKb],
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
            'files'          => $this->files,
            'files_count'    => count($this->files),
            'files_is_array' => is_array($this->files),
        ]);
        $this->commitError = 'Debug: Files count = ' . count($this->files) . ', Type = ' . gettype($this->files);
    }

    public function commit(): void
    {
        $count = is_array($this->files) ? count($this->files) : 0;
        \Log::info('UniversalUploader::commit()', [
            'files_count'  => $count,
            'files_type'   => gettype($this->files),
            'documentMode' => (bool) ($this->documentTypeId && $this->customerId),
        ]);

        $this->commitError = null;

        if ($count < 1) {
            $this->commitError = 'No files to save. Select a file above and wait for it to appear in Staged files.';
            return;
        }

        $this->validate([
            'files'   => ['required', 'array', 'min:1', 'max:' . $this->maxFiles],
            'files.*' => ['max:' . $this->maxSizeKb],
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
            \Log::error('UniversalUploader::commit failed', ['message' => $e->getMessage()]);
            report($e);
        }
    }

    protected function commitAsDocument(): void
    {
        $file = is_array($this->files) ? ($this->files[0] ?? null) : null;
        if (! $file) {
            throw new \RuntimeException('No file to upload.');
        }
        $path = 'customer-documents/' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        Storage::disk('spaces')->put($path, $file->get());
        \Log::info('UniversalUploader: file stored on Spaces', ['path' => $path]);

        \App\Models\CustomerDocument::create([
            'customer_id'      => $this->customerId,
            'document_type_id' => $this->documentTypeId,
            'file_name'        => $file->getClientOriginalName(),
            'file_path'        => $path,
            'file_format'      => $file->getClientOriginalExtension(),
            'document_number'  => $this->documentNumber ?: '',
            'valid_until'      => $this->validUntil ?: null,
            'status'           => 'pending_review',
        ]);
    }

    protected function commitAsMedia(): void
    {
        /** @var Model $model */
        $model = ($this->modelType)::query()->findOrFail($this->modelId);

        foreach ($this->files as $file) {
            $safeName  = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
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
