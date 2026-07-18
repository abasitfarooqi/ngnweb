<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NgnDigitalInvoice;
use App\Support\FluxAdminFormPayload;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Digital invoices — Flux Admin')]
class DigitalInvoiceIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-ecommerce');
        $this->exportable = true;
        $this->exportFilename = 'digital-invoices';
        $this->sortField = 'issue_date';
    }

    protected function formModel(): string { return NgnDigitalInvoice::class; }

    protected function formRules(): array
    {
        return [
            'formData.invoice_number'   => ['nullable', 'string', 'max:100'],
            'formData.invoice_type'     => ['required', 'string'],
            'formData.invoice_category' => ['nullable', 'string', 'max:100'],
            'formData.customer_name'    => ['nullable', 'string', 'max:255'],
            'formData.customer_email'   => ['nullable', 'email', 'max:255'],
            'formData.customer_phone'   => ['nullable', 'string', 'max:50'],
            'formData.registration_number' => ['nullable', 'string', 'max:50'],
            'formData.make'             => ['nullable', 'string', 'max:100'],
            'formData.model'            => ['nullable', 'string', 'max:100'],
            'formData.year'             => ['nullable', 'integer'],
            'formData.vin'              => ['nullable', 'string', 'max:100'],
            'formData.issue_date'       => ['required', 'date'],
            'formData.due_date'         => ['nullable', 'date'],
            'formData.amount'           => ['nullable', 'numeric'],
            'formData.total_paid'       => ['nullable', 'numeric'],
            'formData.status'           => ['required', 'string'],
            'formData.notes'            => ['nullable', 'string'],
            'formData.internal_notes'   => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'issue_date'   => now()->toDateString(),
            'status'       => 'draft',
            'invoice_type' => 'sale',
            'created_by'   => FluxAdminFormPayload::adminUserId(),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = NgnDigitalInvoice::findOrFail($id);
        $this->fillFromModel($record);
        $this->formData['issue_date'] = $record->issue_date ? Carbon::parse($record->issue_date)->format('Y-m-d') : null;
        $this->formData['due_date']   = $record->due_date   ? Carbon::parse($record->due_date)->format('Y-m-d')   : null;
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        NgnDigitalInvoice::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.digital-invoices-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return NgnDigitalInvoice::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('invoice_number', 'like', "%{$v}%")->orWhere('customer_name', 'like', "%{$v}%")->orWhere('customer_email', 'like', "%{$v}%")->orWhere('registration_number', 'like', "%{$v}%")))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('invoice_type'), fn ($q, $v) => $q->where('invoice_type', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'Invoice #' => 'invoice_number', 'Type' => 'invoice_type', 'Category' => 'invoice_category',
            'Customer' => 'customer_name', 'Email' => 'customer_email', 'Phone' => 'customer_phone',
            'Reg' => 'registration_number',
            'Issue date' => fn ($r) => $r->issue_date ? Carbon::parse($r->issue_date)->format('Y-m-d') : '',
            'Due date' => fn ($r) => $r->due_date ? Carbon::parse($r->due_date)->format('Y-m-d') : '',
            'Total' => 'total', 'Paid' => 'total_paid', 'Status' => 'status',
        ];
    }
}
