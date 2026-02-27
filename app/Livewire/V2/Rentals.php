<?php

namespace App\Livewire\V2;

use App\Models\Motorbike;
use Livewire\Component;

class Rentals extends Component
{
    public function render()
    {
        $bikes = Motorbike::where('is_ebike', 0)
            ->latest()
            ->take(12)
            ->get();

        return view('livewire.v2.rentals', compact('bikes'))
            ->layout('v2.layouts.app');
    }
}
