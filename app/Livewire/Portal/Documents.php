<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentType;
use App\Models\CustomerDocument;

class Documents extends Component
{
    use WithFileUploads;

    protected $listeners = ['document-upload-committed' => 'onDocumentUploadCommitted'];

    public $activeTab = 'rental';
    public $uploadingFor = null;
    public $file;
    public $document_number;
    public $valid_until;

    public function switchTab($tab)
    {
        Log::info('Documents: switchTab', ['tab' => $tab]);
        $this->activeTab = $tab;
    }

    public function startUpload($documentTypeId)
    {
        Log::info('Documents: startUpload called', ['documentTypeId' => $documentTypeId]);
        $this->uploadingFor    = (int) $documentTypeId;
        $this->file            = null;
        $this->document_number = '';
        $this->valid_until     = '';
        $this->resetValidation();
    }

    public function cancelUpload()
    {
        $this->uploadingFor = null;
        $this->file         = null;
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
            'file.max'      => 'File must be 10MB or smaller.',
        ]);

        $customerAuth = Auth::guard('customer')->user();
        $profile      = $customerAuth->profile;

        if (!$profile) {
            session()->flash('error', 'Please complete your profile first.');
            return;
        }

        $path = $this->file->store('customer-documents', 'public');

        CustomerDocument::create([
            'customer_id'       => $profile->id,
            'document_type_id'  => $this->uploadingFor,
            'file_name'         => $this->file->getClientOriginalName(),
            'file_path'         => $path,
            'file_format'       => $this->file->getClientOriginalExtension(),
            'document_number'   => $this->document_number ?: '',
            'valid_until'       => $this->valid_until ?: null,
            'status'            => 'pending_review',
        ]);

        session()->flash('success', 'Document uploaded successfully!');
        $this->cancelUpload();
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $profile      = $customerAuth?->profile;
        $customer     = $customerAuth?->customer;

        $rentalDocs  = DocumentType::forRental()->orderBy('sort_order')->get();
        $financeDocs = DocumentType::forFinance()->orderBy('sort_order')->get();

        $uploadedDocs = [];
        if ($customer?->id) {
            $uploadedDocs = CustomerDocument::where('customer_id', $customer->id)
                ->with('documentType')
                ->get()
                ->keyBy('document_type_id');
        }

        return view('livewire.portal.documents', compact('rentalDocs', 'financeDocs', 'uploadedDocs', 'profile'))
            ->layout('components.layouts.portal');
    }
}
