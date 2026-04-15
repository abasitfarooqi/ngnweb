<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\MotorbikeAnnualCompliance;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ComplianceTab extends Component
{
    public int $motorbikeId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $records = MotorbikeAnnualCompliance::where('motorbike_id', $this->motorbikeId)
            ->orderByDesc('year')
            ->get();

        return view('flux-admin.partials.motorbikes.compliance-tab', [
            'records' => $records,
        ]);
    }
}
