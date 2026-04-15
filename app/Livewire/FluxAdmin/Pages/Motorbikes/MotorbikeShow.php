<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Models\Motorbike;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class MotorbikeShow extends Component
{
    public Motorbike $motorbike;

    public string $activeTab = 'details';

    public function mount(Motorbike $motorbike): void
    {
        $this->motorbike = $motorbike->load('vehicleProfile', 'branch');
    }

    public function getTitle(): string
    {
        return $this->motorbike->reg_no . ' — Flux Admin';
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.show');
    }
}
