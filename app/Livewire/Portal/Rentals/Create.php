<?php

namespace App\Livewire\Portal\Rentals;

use Livewire\Component;
use App\Models\Motorbike;

class Create extends Component
{
    public $motorbikeId;
    public $motorbike;

    public function mount($motorbikeId)
    {
        $this->motorbikeId = $motorbikeId;
        $this->motorbike = Motorbike::with(['currentRentingPricing', 'branch'])->find($motorbikeId);

        if (!$this->motorbike) abort(404);
    }

    public function render()
    {
        return view('livewire.portal.rentals.create')
            ->layout('components.layouts.portal');
    }
}
