<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\MotorbikeRepairUpdate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Repair updates — Flux Admin')]
class RepairUpdateIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function delete(int $id): void
    {
        $update = MotorbikeRepairUpdate::findOrFail($id);
        $update->services()->detach();
        $update->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = MotorbikeRepairUpdate::query()
            ->with(['services:id,name', 'motorbikeRepair.motorbike:id,reg_no'])
            ->when($this->search, function ($q, $v): void {
                $q->where(function ($q) use ($v): void {
                    $q->where('job_description', 'like', "%{$v}%")
                        ->orWhere('note', 'like', "%{$v}%")
                        ->orWhere('motorbike_repair_id', $v)
                        ->orWhereHas('motorbikeRepair', function ($repair) use ($v): void {
                            $repair->where('fullname', 'like', "%{$v}%")
                                ->orWhereHas('motorbike', fn ($bike) => $bike->where('reg_no', 'like', "%{$v}%"));
                        });
                });
            })
            ->when($this->filter('motorbike_repair_id'), fn ($q, $v) => $q->where('motorbike_repair_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.repair-updates-index', ['rows' => $rows]);
    }
}
