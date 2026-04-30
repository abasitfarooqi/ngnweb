<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\RentingPricing;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental pricing — Flux Admin')]
class RentingPricingIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-renting-page');
        $this->exportable = true;
        $this->exportFilename = 'rental-pricing';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['motorbike:id,reg_no,make,model', 'user:id,first_name'])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.misc.renting-pricing-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return RentingPricing::query()
            ->when($this->search, fn ($q, $v) => $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%")->orWhere('make', 'like', "%{$v}%")->orWhere('model', 'like', "%{$v}%")))
            ->when($this->filter('iscurrent') !== '', fn ($q) => $q->where('iscurrent', $this->filter('iscurrent') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('motorbike:id,reg_no,make,model');
    }

    protected function exportColumns(): array
    {
        return [
            'Registration' => fn ($r) => $r->motorbike?->reg_no,
            'Make' => fn ($r) => $r->motorbike?->make, 'Model' => fn ($r) => $r->motorbike?->model,
            'Weekly price' => 'weekly_price', 'Minimum deposit' => 'minimum_deposit',
            'Current' => fn ($r) => $r->iscurrent ? 'Yes' : 'No',
            'Effective from' => fn ($r) => $r->update_date ? \Carbon\Carbon::parse($r->update_date)->format('Y-m-d') : '',
        ];
    }
}
