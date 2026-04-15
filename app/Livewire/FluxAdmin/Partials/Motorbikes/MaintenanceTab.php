<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\MotorbikeMaintenanceLog;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class MaintenanceTab extends Component
{
    public int $motorbikeId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $logs = MotorbikeMaintenanceLog::with('user', 'booking')
            ->where('motorbike_id', $this->motorbikeId)
            ->orderByDesc('serviced_at')
            ->get();

        return view('flux-admin.partials.motorbikes.maintenance-tab', [
            'logs' => $logs,
        ]);
    }
}
