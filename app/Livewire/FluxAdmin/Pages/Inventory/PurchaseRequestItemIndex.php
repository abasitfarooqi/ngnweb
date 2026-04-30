<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\PurchaseRequestItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Purchase request items — Flux Admin')]
class PurchaseRequestItemIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function render()
    {
        $rows = PurchaseRequestItem::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('part_number', 'like', "%{$v}%")->orWhere('reg_no', 'like', "%{$v}%")->orWhere('chassis_no', 'like', "%{$v}%")))
            ->when($this->filter('pr_id'), fn ($q, $v) => $q->where('pr_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.inventory.purchase-request-items-index', ['rows' => $rows]);
    }
}
