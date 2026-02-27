<?php

namespace App\Livewire\V2;

use App\Models\Motorbike;
use Livewire\Component;

class EBikes extends Component
{
    public function render()
    {
        $ebikes = Motorbike::where('is_ebike', 1)->latest()->get();

        return view('livewire.v2.ebikes', compact('ebikes'))
            ->layout('v2.layouts.app');
    }
}
