<?php

namespace App\Livewire\Site\Rentals;

use App\Models\Motorbike;
use Livewire\Component;

class Index extends Component
{
    public $rentals = [];

    public function mount()
    {
        $this->rentals = Motorbike::whereHas('rentingPricings')
            ->with('currentRentingPricing')
            ->get();
    }

    public function render()
    {
        return view('livewire.site.rentals.index')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Rentals in London | From £70/week | NGN Motors',
                'description' => 'Rent a motorcycle in London from £70/week. Honda & Yamaha 125cc. CBT friendly.',
            ]);
    }
}
