<?php

namespace App\Livewire\Portal;

use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use App\Models\RentingBooking;
use App\Support\AgreementContractStorage;
use App\Support\CustomerDocumentReviewNotifier;
use App\Support\CustomerDocumentStorage;
use App\Support\PortalDocumentUpload;
use App\Support\RentalBookingLifecycle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Documents extends Component
{
    use WithFileUploads;

    protected $listeners = ['document-upload-committed' => 'onDocumentUploadCommitted'];

    public $activeTab = 'rental';

    public $uploadingFor = null;

    public $file;

    public $valid_until;

    public ?array $lastUploadReceipt = null;

    public ?int $rentalBookingId = null;

    public function mount(): void
    {
        $tab = strtolower((string) request()->query('tab', 'rental'));
        if ($tab === 'other') {
            $tab = 'rental';
        }
        if (in_array($tab, ['rental', 'finance'], true)) {
            $this->activeTab = $tab;
        }

        $bookingId = request()->integer('booking_id') ?: null;
        if ($bookingId) {
            $customerId = $this->getPortalCustomerId();
            $ownsBooking = $customerId && RentingBooking::query()
                ->where('id', $bookingId)
                ->where('customer_id', $customerId)
                ->exists();

            $this->rentalBookingId = $ownsBooking ? $bookingId : null;
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

            if (AgreementContractStorage::isAgreementPdfPath($normalised)) {
                return AgreementContractStorage::appUrl($path, $isPrivate);
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
        $tab = strtolower((string) $tab);
        if (in_array($tab, ['rental', 'finance'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function startUpload($documentTypeId)
    {
        $profile = Auth::guard('customer')->user()?->customer;

        if ($profile && ! $profile->canCustomerEditPortal()) {
            session()->flash('error', 'Document uploads are read-only until NGN authorises your account.');

            return;
        }

        $existing = null;
        $customerId = $this->getPortalCustomerId();

        if ($customerId) {
            $existing = CustomerDocument::query()
                ->where('customer_id', $customerId)
                ->where('document_type_id', (int) $documentTypeId)
                ->first();
        }

        $lifecycle = app(RentalBookingLifecycle::class);
        $status = $lifecycle->resolveCustomerDocumentStatus($existing);

        if ($profile && ! $profile->canCustomerReplaceDocument($status)) {
            session()->flash('error', match ($status) {
                'pending_review' => 'This document is awaiting review and cannot be changed yet.',
                'approved' => 'This document is approved and locked. Contact us if you need to replace it.',
                default => 'You cannot replace this document at the moment.',
            });

            return;
        }

        $this->uploadingFor = (int) $documentTypeId;
        $this->file = null;
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
        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;

        if (! $profile) {
            session()->flash('error', 'Your account is not linked to a customer record yet.');

            return;
        }

        if (! $this->file) {
            session()->flash('error', 'Please select a file first.');
            $this->dispatch('portal-document-upload-popup', message: 'Please select a file first.');

            return;
        }

        try {
            $this->validate([
                'uploadingFor' => 'required|integer|exists:document_types,id',
                'file' => 'required|file|max:10240',
            ]);

            $result = app(PortalDocumentUpload::class)->store(
                $profile,
                (int) $this->uploadingFor,
                $this->file,
                $this->valid_until ?: null,
            );

            session()->flash('success', 'Document uploaded successfully!');
            $this->lastUploadReceipt = [
                'document_type' => optional(DocumentType::query()->find($this->uploadingFor))->name ?: 'Document',
                'file_name' => $result['document']->file_name ?: 'Uploaded file',
                'uploaded_at' => now()->toIso8601String(),
                'storage_target' => $result['storage_target'],
            ];
            $this->dispatch('portal-document-upload-popup', message: $result['synced_now']
                ? 'Upload complete. Synced to storage now.'
                : 'Upload complete.');
            $this->cancelUpload();
        } catch (\Throwable $e) {
            session()->flash('error', 'Upload failed. '.$e->getMessage());
            $this->dispatch('portal-document-upload-popup', message: 'Upload failed: '.$e->getMessage());
        }
    }

    public function deleteDocument(int $documentTypeId): void
    {
        $customerId = $this->getPortalCustomerId();
        $profile = Auth::guard('customer')->user()?->customer;

        if (! $customerId || ! $profile) {
            session()->flash('error', 'Your account is not linked to a customer record yet.');

            return;
        }

        $document = CustomerDocument::query()
            ->where('customer_id', $customerId)
            ->where('document_type_id', $documentTypeId)
            ->first();

        if (! $document) {
            return;
        }

        $lifecycle = app(RentalBookingLifecycle::class);
        $status = $lifecycle->resolveCustomerDocumentStatus($document);

        if (! $profile->canCustomerDeleteDocument($status)) {
            session()->flash('error', 'This document cannot be deleted at the moment.');

            return;
        }

        if ($document->file_path) {
            CustomerDocumentStorage::delete($document->file_path);
        }

        $bookingId = $document->booking_id ? (int) $document->booking_id : null;
        $document->delete();

        app(CustomerDocumentReviewNotifier::class)->clearStaffMandatorySubmittedFlag($customerId, $bookingId);

        session()->flash('success', 'Document removed.');
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;
        $customerId = $this->getPortalCustomerId();

        $financeDocs = DocumentType::query()->forFinance()->orderBy('sort_order')->orderBy('name')->get();
        $financeDocIds = $financeDocs->pluck('id');

        $rentalAndGeneralDocs = DocumentType::query()
            ->when($financeDocIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $financeDocIds->all()))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $rentalGeneralDocIds = $rentalAndGeneralDocs->pluck('id');

        $uploadedDocuments = collect();
        $uploadedByType = collect();
        $rentalUploadedDocuments = collect();
        $financeUploadedDocuments = collect();
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
                    ->filter(fn (CustomerDocument $doc) => $rentalGeneralDocIds->contains($doc->document_type_id))
                    ->values();

                $financeUploadedDocuments = $uploadedDocuments
                    ->filter(fn (CustomerDocument $doc) => $financeDocIds->contains($doc->document_type_id))
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

        $rentalMandatoryIds = $rentalAndGeneralDocs->where('is_mandatory', true)->pluck('id');
        $financeMandatoryIds = $financeDocs->where('is_mandatory', true)->pluck('id');
        $documentLifecycle = app(RentalBookingLifecycle::class);

        $missingRentalMandatory = $rentalMandatoryIds->filter(function ($id) use ($uploadedByType, $documentLifecycle) {
            $doc = $uploadedByType->get($id);
            $status = $documentLifecycle->resolveCustomerDocumentStatus($doc);

            return $documentLifecycle->documentNeedsUpload($status);
        })->values();

        $missingFinanceMandatory = $financeMandatoryIds->filter(function ($id) use ($uploadedByType, $documentLifecycle) {
            $doc = $uploadedByType->get($id);
            $status = $documentLifecycle->resolveCustomerDocumentStatus($doc);

            return $documentLifecycle->documentNeedsUpload($status);
        })->values();

        return view('livewire.portal.documents', compact(
            'rentalAndGeneralDocs',
            'financeDocs',
            'uploadedByType',
            'uploadedDocuments',
            'rentalUploadedDocuments',
            'financeUploadedDocuments',
            'rentalAgreements',
            'financeContracts',
            'profile',
            'customerId',
            'missingRentalMandatory',
            'missingFinanceMandatory',
            'documentLifecycle',
        ))
            ->layout('components.layouts.portal', ['title' => 'My Documents | My Account']);
    }
}
