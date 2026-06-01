<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class DocumentsSection extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function deleteDocument(int $id): void
    {
        $document = CustomerDocument::find($id);

        if (! $document || empty($document->file_path)) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Document file not found.');

            return;
        }

        $sourcePath = ltrim($document->file_path, '/');
        $diskPublic  = Storage::disk('public');
        $diskLocal   = Storage::disk('local');
        $diskPrivate = Storage::disk('private');

        $fromDisk = null;

        if ($diskPublic->exists($sourcePath)) {
            $fromDisk = $diskPublic;
        } elseif ($diskLocal->exists($sourcePath)) {
            $fromDisk = $diskLocal;
        } elseif ($diskPrivate->exists($sourcePath)) {
            $document->sent_private = true;
            $document->save();
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Document already in private storage.');

            return;
        }

        if ($fromDisk) {
            try {
                $diskPrivate->makeDirectory(dirname($sourcePath));
                $diskPrivate->put($sourcePath, $fromDisk->get($sourcePath));
                $fromDisk->delete($sourcePath);
            } catch (\Throwable $e) {
                Log::error("Failed moving document file {$sourcePath}: {$e->getMessage()}");
                $this->dispatch('flux-admin:toast', type: 'error', message: 'Failed to move document file.');

                return;
            }
        }

        $document->sent_private = true;
        $document->save();

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Document moved to private storage.');
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $documents = $this->customer->customerDocuments()->with('documentType')->orderByDesc('id')->get();

        return view('flux-admin.partials.customers.documents-section', compact('documents'));
    }
}
