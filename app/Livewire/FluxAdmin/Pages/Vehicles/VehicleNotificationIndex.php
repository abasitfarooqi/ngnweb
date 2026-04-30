<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\VehicleNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle notifications — Flux Admin')]
class VehicleNotificationIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function render()
    {
        $rows = VehicleNotification::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('reg_no', 'like', "%{$v}%")))
            ->when($this->filter('enable') !== '', fn ($q) => $q->where('enable', $this->filter('enable') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.vehicle-notifications-index', ['rows' => $rows]);
    }
}
