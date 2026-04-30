<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\DsOrder;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Delivery service orders — Flux Admin')]
class DsOrderIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'ds-orders';
        $this->sortField = 'pick_up_datetime';
    }

    public function toggleProceed(int $id): void
    {
        $o = DsOrder::findOrFail($id);
        $o->proceed = ! $o->proceed;
        $o->save();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Updated.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->withCount('dsOrderItems')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.ds-orders-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return DsOrder::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('full_name', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")->orWhere('postcode', 'like', "%{$v}%")))
            ->when($this->filter('proceed') !== '', fn ($q) => $q->where('proceed', $this->filter('proceed') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Pickup' => fn ($r) => $r->pick_up_datetime ? \Carbon\Carbon::parse($r->pick_up_datetime)->format('Y-m-d H:i') : '',
            'Customer' => 'full_name', 'Phone' => 'phone', 'Address' => 'address', 'Postcode' => 'postcode',
            'Proceed' => fn ($r) => $r->proceed ? 'Yes' : 'No',
        ];
    }
}
