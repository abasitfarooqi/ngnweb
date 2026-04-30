<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClaimMotorbike;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike claims — Flux Admin')]
class ClaimIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'case_date';
    }

    public function render()
    {
        $rows = ClaimMotorbike::query()
            ->with(['motorbike:id,reg_no', 'branch:id,name'])
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('fullname', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")->orWhereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%"))))
            ->when($this->filter('is_received') !== '', fn ($q) => $q->where('is_received', $this->filter('is_received') === '1'))
            ->when($this->filter('is_returned') !== '', fn ($q) => $q->where('is_returned', $this->filter('is_returned') === '1'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.claims-index', ['rows' => $rows]);
    }
}
