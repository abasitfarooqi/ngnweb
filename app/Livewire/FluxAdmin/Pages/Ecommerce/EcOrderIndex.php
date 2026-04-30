<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Ecommerce\EcOrder;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('E-commerce orders — Flux Admin')]
class EcOrderIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'ecommerce-orders';
        $this->sortField = 'order_date';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['customer:id,email', 'branch:id,name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.orders-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return EcOrder::query()
            ->when($this->search, fn ($q, $v) => $q->whereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")))
            ->when($this->filter('order_status'), fn ($q, $v) => $q->where('order_status', $v))
            ->when($this->filter('payment_status'), fn ($q, $v) => $q->where('payment_status', $v))
            ->when($this->filter('shipping_status'), fn ($q, $v) => $q->where('shipping_status', $v));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with(['customer', 'branch']);
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Date' => fn ($r) => $r->order_date ? \Carbon\Carbon::parse($r->order_date)->format('Y-m-d') : '',
            'Customer' => fn ($r) => $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '',
            'Email' => fn ($r) => $r->customer?->email,
            'Branch' => fn ($r) => $r->branch?->name,
            'Grand total' => 'grand_total', 'Currency' => 'currency',
            'Order status' => 'order_status', 'Payment' => 'payment_status', 'Shipping' => 'shipping_status',
        ];
    }
}
