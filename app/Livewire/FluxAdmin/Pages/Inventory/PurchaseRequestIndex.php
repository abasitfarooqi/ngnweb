<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Purchase requests — Flux Admin')]
class PurchaseRequestIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); $this->sortField = 'date'; }

    public function render()
    {
        $rows = PurchaseRequest::query()
            ->withCount('items')
            ->when($this->search, fn ($q, $v) => $q->where('note', 'like', "%{$v}%"))
            ->when($this->filter('is_posted') !== '', fn ($q) => $q->where('is_posted', $this->filter('is_posted') === '1'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.inventory.purchase-requests-index', ['rows' => $rows]);
    }
}
