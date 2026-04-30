<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NgnDigitalInvoice;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Digital invoices — Flux Admin')]
class DigitalInvoiceIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'digital-invoices';
        $this->sortField = 'issue_date';
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

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'Invoice #' => 'invoice_number', 'Type' => 'invoice_type', 'Category' => 'invoice_category',
            'Customer' => 'customer_name', 'Email' => 'customer_email', 'Phone' => 'customer_phone',
            'Reg' => 'registration_number',
            'Issue date' => fn ($r) => $r->issue_date ? \Carbon\Carbon::parse($r->issue_date)->format('Y-m-d') : '',
            'Due date' => fn ($r) => $r->due_date ? \Carbon\Carbon::parse($r->due_date)->format('Y-m-d') : '',
            'Total' => 'total', 'Paid' => 'total_paid', 'Status' => 'status',
        ];
    }
}
