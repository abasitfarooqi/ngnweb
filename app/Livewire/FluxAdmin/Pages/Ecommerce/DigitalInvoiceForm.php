<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnDigitalInvoice;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Digital Invoice — Flux Admin')]
class DigitalInvoiceForm extends Component
{
    use WithAuthorization;

    public ?NgnDigitalInvoice $digitalInvoice = null;

    public array $form = [];

    public function mount(?NgnDigitalInvoice $digitalInvoice = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-ecommerce');
        $this->digitalInvoice = $digitalInvoice?->id ? $digitalInvoice : null;

        if ($this->digitalInvoice) {
            $attrs = $this->digitalInvoice->getAttributes();
            $attrs['issue_date'] = $this->digitalInvoice->issue_date ? Carbon::parse($this->digitalInvoice->issue_date)->format('Y-m-d') : null;
            $attrs['due_date']   = $this->digitalInvoice->due_date   ? Carbon::parse($this->digitalInvoice->due_date)->format('Y-m-d')   : null;
            $this->form = $attrs;
        } else {
            $this->form = ['issue_date' => now()->toDateString(), 'status' => 'draft', 'invoice_type' => 'sale'];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.invoice_number'      => ['nullable', 'string', 'max:100'],
            'form.invoice_type'        => ['required', 'string'],
            'form.invoice_category'    => ['nullable', 'string', 'max:100'],
            'form.customer_name'       => ['nullable', 'string', 'max:255'],
            'form.customer_email'      => ['nullable', 'email', 'max:255'],
            'form.customer_phone'      => ['nullable', 'string', 'max:50'],
            'form.registration_number' => ['nullable', 'string', 'max:50'],
            'form.make'                => ['nullable', 'string', 'max:100'],
            'form.model'               => ['nullable', 'string', 'max:100'],
            'form.year'                => ['nullable', 'integer'],
            'form.vin'                 => ['nullable', 'string', 'max:100'],
            'form.issue_date'          => ['required', 'date'],
            'form.due_date'            => ['nullable', 'date'],
            'form.amount'              => ['nullable', 'numeric'],
            'form.total_paid'          => ['nullable', 'numeric'],
            'form.status'              => ['required', 'string'],
            'form.notes'               => ['nullable', 'string'],
            'form.internal_notes'      => ['nullable', 'string'],
        ]);

        $payload = [
            'invoice_number'      => $this->form['invoice_number'] ?? null,
            'invoice_type'        => $this->form['invoice_type'],
            'invoice_category'    => $this->form['invoice_category'] ?? null,
            'customer_name'       => $this->form['customer_name'] ?? null,
            'customer_email'      => $this->form['customer_email'] ?? null,
            'customer_phone'      => $this->form['customer_phone'] ?? null,
            'registration_number' => $this->form['registration_number'] ?? null,
            'make'                => $this->form['make'] ?? null,
            'model'               => $this->form['model'] ?? null,
            'year'                => $this->form['year'] ?? null,
            'vin'                 => $this->form['vin'] ?? null,
            'issue_date'          => $this->form['issue_date'],
            'due_date'            => $this->form['due_date'] ?: null,
            'amount'              => $this->form['amount'] ?? null,
            'total_paid'          => $this->form['total_paid'] ?? null,
            'status'              => $this->form['status'],
            'notes'               => $this->form['notes'] ?? null,
            'internal_notes'      => $this->form['internal_notes'] ?? null,
        ];

        if ($this->digitalInvoice) {
            $this->digitalInvoice->update($payload);
        } else {
            NgnDigitalInvoice::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Invoice saved.');
        $this->redirect(route('flux-admin.digital-invoices.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.ecommerce.digital-invoice-form');
    }
}
