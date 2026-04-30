<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ClubMemberPurchase;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club purchases — Flux Admin')]
class PurchaseIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'club-purchases';
        $this->sortField = 'date';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['clubMember.customer:id,first_name,last_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.purchases-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return ClubMemberPurchase::query()
            ->when($this->search, fn ($q, $v) => $q->where('pos_invoice', 'like', "%{$v}%")->orWhereHas('clubMember.customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->when($this->filter('is_redeemed') !== '', fn ($q) => $q->where('is_redeemed', $this->filter('is_redeemed') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with(['clubMember.customer']);
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Date' => fn ($r) => $r->date ? \Carbon\Carbon::parse($r->date)->format('Y-m-d') : '',
            'POS invoice' => 'pos_invoice', 'Branch ID' => 'branch_id',
            'Member' => fn ($r) => $r->clubMember?->customer ? $r->clubMember->customer->first_name.' '.$r->clubMember->customer->last_name : '',
            'Price' => 'price', 'Total' => 'total', 'Percent' => 'percent', 'Discount' => 'discount',
            'Redeemed' => fn ($r) => $r->is_redeemed ? 'Yes' : 'No', 'Redeem amount' => 'redeem_amount',
        ];
    }
}
