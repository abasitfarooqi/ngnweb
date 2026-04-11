<?php

namespace App\Livewire\Portal;

use App\Jobs\MoveCustomerDocumentToSpacesJob;
use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use App\Support\CustomerDocumentStorage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Documents extends Component
{
    use WithFileUploads;

    protected $listeners = ['document-upload-committed' => 'onDocumentUploadCommitted'];

    public $activeTab = 'rental';

    public $uploadingFor = null;

    public $file;

    public $document_number;

    public $valid_until;

    public ?array $lastUploadReceipt = null;

    public function mount(): void
    {
        $tab = strtolower((string) request()->query('tab', 'rental'));
        if (in_array($tab, ['rental', 'finance', 'other'], true)) {
            $this->activeTab = $tab;
        }
    }

    protected function getPortalCustomerId(): ?int
    {
        $customerAuth = Auth::guard('customer')->user();

        return $customerAuth?->customer_id ?? $customerAuth?->customer?->id;
    }

    protected function resolveStoredFileUrl(?string $path, bool $isPrivate = false): ?string
    {
        if (! $path || $isPrivate) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalised = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
        if ($normalised === '') {
            return null;
        }

        try {
            if (str_starts_with($normalised, 'customer-documents/')) {
                return CustomerDocumentStorage::urlForPath($path) ?? url('/storage/'.$normalised);
            }

            if (str_starts_with($normalised, 'customers/')) {
                return Storage::disk('public')->url($normalised);
            }

            return Storage::disk('public')->url($normalised);
        } catch (\Throwable) {
            return str_starts_with($path, '/storage/')
                ? url($path)
                : url('/storage/'.$normalised);
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function startUpload($documentTypeId)
    {
        $this->uploadingFor = (int) $documentTypeId;
        $this->file = null;
        $this->document_number = '';
        $this->valid_until = '';
        $this->resetValidation();
    }

    public function cancelUpload()
    {
        $this->uploadingFor = null;
        $this->file = null;
        $this->resetValidation();
    }

    public function onDocumentUploadCommitted(
        ?int $documentTypeId = null,
        ?string $fileName = null,
        ?string $uploadedAt = null,
        ?string $storageTarget = null
    ): void {
        $documentTypeName = null;
        if ($documentTypeId) {
            $documentTypeName = DocumentType::query()->whereKey($documentTypeId)->value('name');
        }

        $this->lastUploadReceipt = [
            'document_type' => $documentTypeName ?: 'Document',
            'file_name' => $fileName ?: 'Uploaded file',
            'uploaded_at' => $uploadedAt ?: now()->toIso8601String(),
            'storage_target' => $storageTarget ?: 'site-storage',
        ];

        session()->flash('success', 'Document uploaded successfully and saved.');
        $this->cancelUpload();
    }

    public function submitDocumentUpload()
    {
        \Log::info('Portal Documents::submitDocumentUpload called', [
            'uploading_for' => $this->uploadingFor,
            'has_file' => (bool) $this->file,
        ]);

        try {
            $this->validate([
                'uploadingFor' => 'required|integer|exists:document_types,id',
                'file' => 'required|file|max:10240',
            ], [
                'uploadingFor.required' => 'Please choose a document type first.',
                'file.required' => 'Please select a file first.',
                'file.max' => 'File must be 10MB or smaller.',
            ]);
        } catch (ValidationException $e) {
            $firstError = $e->validator->errors()->first() ?: 'Validation failed.';
            \Log::warning('Portal Documents::submitDocumentUpload validation failed', [
                'uploading_for' => $this->uploadingFor,
                'error' => $firstError,
            ]);
            $this->dispatch('portal-document-upload-popup', message: $firstError);
            throw $e;
        }

        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;
        \Log::info('Portal Documents::submitDocumentUpload customer guard resolved', [
            'has_customer_auth' => (bool) $customerAuth,
            'has_profile' => (bool) $profile,
            'customer_auth_id' => $customerAuth?->id,
        ]);

        if (! $profile) {
            \Log::warning('Portal Documents::submitDocumentUpload blocked: no customer profile linked');
            session()->flash('error', 'Your account is not linked to a customer record yet.');
            $this->dispatch('portal-document-upload-popup', message: 'Upload failed: account is not linked to customer profile.');

            return;
        }

        $customerId = $this->getPortalCustomerId();
        \Log::info('Portal Documents::submitDocumentUpload customer id resolved', ['customer_id' => $customerId]);
        if (! $customerId) {
            \Log::warning('Portal Documents::submitDocumentUpload blocked: empty customer id');
            session()->flash('error', 'Your account is not linked to a customer record yet.');
            $this->dispatch('portal-document-upload-popup', message: 'Upload failed: customer id missing.');

            return;
        }

        try {
            $path = 'customer-documents/'.Str::uuid()->toString().'.'.$this->file->getClientOriginalExtension();
            \Log::info('Portal Documents::submitDocumentUpload storing file', ['path' => $path]);
            CustomerDocumentStorage::put($path, $this->file->get());
            \Log::info('Portal Documents::submitDocumentUpload file stored locally', ['path' => $path]);

            $existing = CustomerDocument::query()->where([
                'customer_id' => $customerId,
                'document_type_id' => $this->uploadingFor,
            ])->first();

            $oldPath = $existing?->file_path;

            $attributes = [
                'customer_id' => $customerId,
                'document_type_id' => $this->uploadingFor,
                'file_name' => $this->file->getClientOriginalName(),
                'file_path' => $path,
                'file_format' => $this->file->getClientOriginalExtension(),
                'document_number' => $this->document_number ?: '',
                'valid_until' => $this->valid_until ?: null,
            ];
            if (Schema::hasColumn('customer_documents', 'status')) {
                $attributes['status'] = 'pending_review';
            }

            \Log::info('Portal Documents::submitDocumentUpload saving db row', [
                'customer_id' => $customerId,
                'document_type_id' => $this->uploadingFor,
            ]);
            $row = CustomerDocument::updateOrCreate([
                'customer_id' => $customerId,
                'document_type_id' => $this->uploadingFor,
            ], $attributes);
            \Log::info('Portal Documents::submitDocumentUpload db row saved', ['document_id' => $row->id]);

            if ($oldPath && $oldPath !== $path) {
                \Log::info('Portal Documents::submitDocumentUpload deleting old file', ['old_path' => $oldPath]);
                CustomerDocumentStorage::delete($oldPath);
            }

            MoveCustomerDocumentToSpacesJob::dispatch($row->id, $path)
                ->delay(now()->addMinutes(10));
            \Log::info('Portal Documents::submitDocumentUpload queued delayed spaces job', ['document_id' => $row->id]);

            // Try immediate sync for quicker DO visibility; fallback remains safe.
            $syncedNow = CustomerDocumentStorage::moveToSpacesAndDeleteLocalIfSynced($path);
            \Log::info('Portal Documents::submitDocumentUpload immediate spaces sync result', [
                'path' => $path,
                'synced_now' => $syncedNow,
            ]);

            session()->flash('success', 'Document uploaded successfully!');
            $this->lastUploadReceipt = [
                'document_type' => optional(DocumentType::query()->find($this->uploadingFor))->name ?: 'Document',
                'file_name' => $row->file_name ?: 'Uploaded file',
                'uploaded_at' => now()->toIso8601String(),
                'storage_target' => $syncedNow
                    ? 'digitalocean-spaces (synced now)'
                    : (CustomerDocumentStorage::spacesConfigured()
                        ? 'site-storage (queued for digitalocean-spaces)'
                        : 'site-storage'),
            ];

            $this->dispatch('portal-document-upload-popup', message: $syncedNow
                ? 'Upload complete. Synced to DigitalOcean now.'
                : 'Upload complete. Saved on site storage and queued for DigitalOcean sync.');
            \Log::info('Portal Documents::submitDocumentUpload completed successfully', ['document_id' => $row->id]);
            $this->cancelUpload();
        } catch (\Throwable $e) {
            \Log::error('Portal Documents::submitDocumentUpload failed', [
                'message' => $e->getMessage(),
                'uploading_for' => $this->uploadingFor,
            ]);
            session()->flash('error', 'Upload failed. '.$e->getMessage());
            $this->dispatch('portal-document-upload-popup', message: 'Upload failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;
        $customerId = $this->getPortalCustomerId();

        $rentalDocs = DocumentType::query()->forRental()->orderBy('sort_order')->orderBy('name')->get();
        $financeDocs = DocumentType::query()->forFinance()->orderBy('sort_order')->orderBy('name')->get();
        $rentalDocIds = $rentalDocs->pluck('id');
        $financeDocIds = $financeDocs->pluck('id');

        $otherDocs = DocumentType::query()
            ->whereNotIn('id', $rentalDocIds->merge($financeDocIds)->unique()->values())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $uploadedDocuments = collect();
        $uploadedByType = collect();
        $rentalUploadedDocuments = collect();
        $financeUploadedDocuments = collect();
        $otherUploadedDocuments = collect();
        $rentalAgreements = collect();
        $financeContracts = collect();

        if ($customerId) {
            try {
                $uploadedDocuments = CustomerDocument::where('customer_id', $customerId)
                    ->with('documentType')
                    ->latest('id')
                    ->get();

                $uploadedDocuments = $uploadedDocuments->map(function (CustomerDocument $doc) {
                    $doc->portal_file_url = $this->resolveStoredFileUrl($doc->file_path, (bool) ($doc->sent_private ?? false));

                    return $doc;
                });

                $uploadedByType = $uploadedDocuments
                    ->sortByDesc('id')
                    ->unique('document_type_id')
                    ->keyBy('document_type_id');

                $rentalUploadedDocuments = $uploadedDocuments
                    ->filter(fn (CustomerDocument $doc) => $rentalDocIds->contains($doc->document_type_id))
                    ->values();

                $financeUploadedDocuments = $uploadedDocuments
                    ->filter(fn (CustomerDocument $doc) => $financeDocIds->contains($doc->document_type_id))
                    ->values();

                $otherUploadedDocuments = $uploadedDocuments
                    ->filter(fn (CustomerDocument $doc) => $otherDocs->pluck('id')->contains($doc->document_type_id))
                    ->values();

                $rentalAgreements = CustomerAgreement::query()
                    ->where('customer_id', $customerId)
                    ->latest('id')
                    ->get()
                    ->map(function (CustomerAgreement $agreement) {
                        $agreement->portal_file_url = $this->resolveStoredFileUrl($agreement->file_path, (bool) ($agreement->sent_private ?? false));

                        return $agreement;
                    });

                $financeContracts = CustomerContract::query()
                    ->where('customer_id', $customerId)
                    ->latest('id')
                    ->get()
                    ->map(function (CustomerContract $contract) {
                        $contract->portal_file_url = $this->resolveStoredFileUrl($contract->file_path, (bool) ($contract->sent_private ?? false));

                        return $contract;
                    });
            } catch (\Exception $e) {
                $uploadedDocuments = collect();
                $uploadedByType = collect();
            }
        }

        $rentalMandatoryIds = $rentalDocs->where('is_mandatory', true)->pluck('id');
        $financeMandatoryIds = $financeDocs->where('is_mandatory', true)->pluck('id');

        $missingRentalMandatory = $rentalMandatoryIds->filter(fn ($id) => ! $uploadedByType->has($id))->values();
        $missingFinanceMandatory = $financeMandatoryIds->filter(fn ($id) => ! $uploadedByType->has($id))->values();

        return view('livewire.portal.documents', compact(
            'rentalDocs',
            'financeDocs',
            'otherDocs',
            'uploadedByType',
            'uploadedDocuments',
            'rentalUploadedDocuments',
            'financeUploadedDocuments',
            'otherUploadedDocuments',
            'rentalAgreements',
            'financeContracts',
            'profile',
            'customerId',
            'missingRentalMandatory',
            'missingFinanceMandatory'
        ))
            ->layout('components.layouts.portal', ['title' => 'My Documents | My Account']);
    }
}
