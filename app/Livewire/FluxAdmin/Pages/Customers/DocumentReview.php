<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\CustomerDocument;
use App\Support\RentalBookingLifecycle;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Review document — Flux Admin')]
class DocumentReview extends Component
{
    use WithAuthorization;

    public CustomerDocument $document;

    public string $status = 'pending_review';

    public ?string $valid_until = null;

    public ?string $rejection_reason = null;

    public function mount(CustomerDocument $document): void
    {
        $this->authorizeModule('see-menu-commons');

        $this->document = $document->load(['customer:id,first_name,last_name,email', 'documentType:id,name']);
        $this->status = (string) ($document->status ?? 'pending_review');
        $this->valid_until = $document->valid_until ? \Carbon\Carbon::parse($document->valid_until)->format('Y-m-d') : null;
        $this->rejection_reason = $document->rejection_reason;
    }

    public function render()
    {
        return view('flux-admin.pages.customers.document-review');
    }

    public function save()
    {
        $this->validate([
            'status' => ['required', Rule::in(['uploaded', 'pending_review', 'approved', 'rejected', 'archived'])],
            'valid_until' => ['nullable', 'date'],
            'rejection_reason' => [
                'nullable', 'string', 'max:2000',
                Rule::requiredIf(fn () => $this->status === 'rejected'),
            ],
        ]);

        if (in_array($this->status, ['approved', 'rejected', 'pending_review'], true)) {
            app(RentalBookingLifecycle::class)->setCustomerDocumentReviewStatus(
                $this->document,
                $this->status,
                $this->status === 'rejected' ? $this->rejection_reason : null
            );
        } else {
            $this->document->fill([
                'status' => $this->status,
                'rejection_reason' => $this->status === 'rejected' ? $this->rejection_reason : null,
            ])->save();
        }

        $this->document->update(['valid_until' => $this->valid_until ?: null]);

        $this->document->refresh();

        session()->flash('flux-admin.flash', 'Document saved.');

        return redirect()->route('flux-admin.customer-documents.index');
    }

    public function setProfileEditingUnlocked(bool $unlocked): void
    {
        $customer = $this->document->customer;
        if (! $customer) {
            return;
        }

        $customer->update(['profile_editing_unlocked' => $unlocked]);
        $this->document->load('customer');
        session()->flash('flux-admin.flash', $unlocked
            ? 'Customer may edit their profile again.'
            : 'Customer profile editing locked.');
    }

    public function setDocumentReuploadUnlocked(bool $unlocked): void
    {
        $customer = $this->document->customer;
        if (! $customer) {
            return;
        }

        $customer->update(['document_reupload_unlocked' => $unlocked]);
        $this->document->load('customer');
        session()->flash('flux-admin.flash', $unlocked
            ? 'Customer may replace approved documents.'
            : 'Approved document re-upload locked.');
    }
}
