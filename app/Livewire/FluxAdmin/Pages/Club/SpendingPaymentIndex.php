<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClubMemberSpendingPayment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club spending payments — Flux Admin')]
class SpendingPaymentIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'date';
    }

    public function render()
    {
        $rows = ClubMemberSpendingPayment::query()
            ->with(['clubMember.customer:id,first_name,last_name'])
            ->when($this->search, fn ($q, $v) => $q->where('pos_invoice', 'like', "%{$v}%")->orWhereHas('clubMember.customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.spending-payments-index', ['rows' => $rows]);
    }
}
