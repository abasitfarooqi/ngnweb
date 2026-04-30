<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\RecoveredMotorbike;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Recovered motorbikes — Flux Admin')]
class RecoveredIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'case_date';
    }

    public function render()
    {
        $rows = RecoveredMotorbike::query()
            ->with(['motorbike:id,reg_no,make,model', 'branch:id,name'])
            ->when($this->search, fn ($q, $v) => $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%")))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.recovered-index', ['rows' => $rows]);
    }
}
