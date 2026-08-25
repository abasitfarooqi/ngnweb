<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\MotorbikeRepair;
use App\Models\MotorbikeRepairUpdate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Repair update — Flux Admin')]
class RepairUpdateForm extends Component
{
    use WithAuthorization;

    public string $repairSearch = '';

    public array $repairSuggestions = [];

    public function mount(?MotorbikeRepairUpdate $motorbikeRepairUpdate = null): void
    {
        $this->authorizeModule('see-menu-services-and-repairs-and-report');

        if ($motorbikeRepairUpdate && $motorbikeRepairUpdate->exists && $motorbikeRepairUpdate->motorbike_repair_id) {
            $this->redirectToRepair((int) $motorbikeRepairUpdate->motorbike_repair_id);

            return;
        }

        $repairId = (int) request('repair', request('motorbike_repair_id', 0));
        if ($repairId > 0 && MotorbikeRepair::query()->whereKey($repairId)->exists()) {
            $this->redirectToRepair($repairId);
        }
    }

    public function updatingRepairSearch(): void
    {
        $term = trim($this->repairSearch);
        if ($term === '') {
            $this->repairSuggestions = [];

            return;
        }

        $this->repairSuggestions = MotorbikeRepair::query()
            ->with('motorbike:id,reg_no')
            ->where(function ($query) use ($term): void {
                $query->where('fullname', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhereHas('motorbike', fn ($bike) => $bike->where('reg_no', 'like', "%{$term}%"));
                if (ctype_digit($term)) {
                    $query->orWhere('motorbikes_repair.id', (int) $term)
                        ->orWhere('motorbike_id', (int) $term);
                }
            })
            ->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'motorbike_id', 'fullname'])
            ->map(fn (MotorbikeRepair $repair) => [
                'id' => $repair->id,
                'label' => 'Repair #'.$repair->id
                    .' · '.($repair->motorbike?->reg_no ?: 'no reg')
                    .' · '.($repair->fullname ?: 'no name'),
            ])
            ->all();
    }

    public function openRepair(int $id): void
    {
        $this->redirectToRepair($id);
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.repair-update-form');
    }

    private function redirectToRepair(int $id): void
    {
        $this->redirect(route('flux-admin.motorbike-repairs.edit', $id), navigate: true);
    }
}
