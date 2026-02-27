<?php

namespace App\Livewire\V2;

use Livewire\Component;

class ServiceComparison extends Component
{
    public function render()
    {
        return view('livewire.v2.service-comparison')
            ->layout('v2.layouts.app');
    }
}
