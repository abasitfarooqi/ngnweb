<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\MotorbikeRegistration;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class RegistrationsTab extends Component
{
    public int $motorbikeId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $registrations = MotorbikeRegistration::where('motorbike_id', $this->motorbikeId)
            ->orderByDesc('start_date')
            ->get();

        return view('flux-admin.partials.motorbikes.registrations-tab', [
            'registrations' => $registrations,
        ]);
    }
}
