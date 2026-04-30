<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\CompanyVehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Company vehicles — Flux Admin')]
class CompanyVehicleIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function render()
    {
        $rows = CompanyVehicle::query()
            ->with('motorbike:id,reg_no,make,model')
            ->when($this->search, fn ($q, $v) => $q->where('custodian', 'like', "%{$v}%")->orWhereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%")))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.company-vehicles-index', ['rows' => $rows]);
    }
}
