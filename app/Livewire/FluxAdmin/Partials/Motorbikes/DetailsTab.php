<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\Motorbike;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class DetailsTab extends Component
{
    public int $motorbikeId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $motorbike = Motorbike::with('vehicleProfile', 'branch')->findOrFail($this->motorbikeId);

        return view('flux-admin.partials.motorbikes.details-tab', [
            'motorbike' => $motorbike,
        ]);
    }
}
