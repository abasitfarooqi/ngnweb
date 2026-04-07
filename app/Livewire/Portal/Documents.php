<?php

namespace App\Livewire\Portal;

use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use App\Support\CustomerDocumentStorage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function onDocumentUploadCommitted(): void
    {
        session()->flash('success', 'Document uploaded successfully.');
        $this->cancelUpload();
    }

    public function upload()
    {
        $this->validate([
            'file' => 'required|file|max:10240',
        ], [
            'file.required' => 'Please select a file first.',
            'file.max' => 'File must be 10MB or smaller.',
        ]);

        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth->customer;

        if (! $profile) {
            session()->flash('error', 'Your account is not linked to a customer record yet.');

            return;
        }

        $customerId = $this->getPortalCustomerId();
        if (! $customerId) {
            session()->flash('error', 'Your account is not linked to a customer record yet.');

            return;
        }

        $path = 'customer-documents/'.Str::uuid()->toString().'.'.$this->file->getClientOriginalExtension();
        CustomerDocumentStorage::put($path, $this->file->get());

        CustomerDocument::updateOrCreate([
            'customer_id' => $customerId,
            'document_type_id' => $this->uploadingFor,
        ], [
            'customer_id' => $customerId,
            'document_type_id' => $this->uploadingFor,
            'file_name' => $this->file->getClientOriginalName(),
            'file_path' => $path,
            'file_format' => $this->file->getClientOriginalExtension(),
            'document_number' => $this->document_number ?: '',
            'valid_until' => $this->valid_until ?: null,
            'status' => 'pending_review',
        ]);

        session()->flash('success', 'Document uploaded successfully!');
        $this->cancelUpload();
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
