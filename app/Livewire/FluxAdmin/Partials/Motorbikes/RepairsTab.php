<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\MotorbikeRepair;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class RepairsTab extends Component
{
    public int $motorbikeId;

    public ?int $expandedRepairId = null;

    public function toggleRepair(int $id): void
    {
        $this->expandedRepairId = $this->expandedRepairId === $id ? null : $id;
    }

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $repairs = MotorbikeRepair::with('updates.services', 'observations', 'branch')
            ->where('motorbike_id', $this->motorbikeId)
            ->orderByDesc('id')
            ->get();

        return view('flux-admin.partials.motorbikes.repairs-tab', [
            'repairs' => $repairs,
        ]);
    }
}
