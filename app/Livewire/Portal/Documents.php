<?php

namespace App\Livewire\Portal;

use App\Models\CustomerDocument;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
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

    protected function getPortalCustomerId(): ?int
    {
        $customerAuth = Auth::guard('customer')->user();

        return $customerAuth?->customer_id ?? $customerAuth?->customer?->id;
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
        session()->flash('success', 'Document uploaded to DO Spaces.');
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

        $path = $this->file->store('customer-documents', 'spaces');

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

        $rentalDocs = DocumentType::query()->forRental()->orderBy('sort_order')->get();
        $financeDocs = DocumentType::query()->forFinance()->orderBy('sort_order')->get();

        $uploadedDocs = collect();
        if ($customerId) {
            try {
                $uploadedDocs = CustomerDocument::where('customer_id', $customerId)
                    ->with('documentType')
                    ->latest('id')
                    ->get()
                    ->unique('document_type_id')
                    ->keyBy('document_type_id');
            } catch (\Exception $e) {
                $uploadedDocs = collect();
            }
        }

        return view('livewire.portal.documents', compact('rentalDocs', 'financeDocs', 'uploadedDocs', 'profile', 'customerId'))
            ->layout('components.layouts.portal', ['title' => 'My Documents | My Account']);
    }
}
