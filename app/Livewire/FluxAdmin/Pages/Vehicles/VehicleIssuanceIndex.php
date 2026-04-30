<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\VehicleIssuance;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle issuances — Flux Admin')]
class VehicleIssuanceIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-renting-page');
    }

    public function render()
    {
        $rows = VehicleIssuance::query()
            ->with(['customer:id,first_name,last_name', 'motorbike:id,reg_no,make,model', 'branch:id,name', 'user:id,first_name'])
            ->when($this->search, fn ($q, $v) => $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%"))->orWhereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->when($this->filter('is_returned') !== '', fn ($q) => $q->where('is_returned', $this->filter('is_returned') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.vehicle-issuances-index', ['rows' => $rows]);
    }
}
