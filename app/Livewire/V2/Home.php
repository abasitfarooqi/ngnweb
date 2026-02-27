<?php

namespace App\Livewire\V2;

use App\Models\MotorbikesSale;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $featuredBikes = MotorbikesSale::with(['motorbike', 'motorbikeImage'])
            ->where('is_sold', 0)
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.v2.home', compact('featuredBikes'))
            ->layout('v2.layouts.app');
    }
}
