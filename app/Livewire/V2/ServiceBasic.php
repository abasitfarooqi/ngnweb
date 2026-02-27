<?php

namespace App\Livewire\V2;

use Livewire\Component;

class ServiceBasic extends Component
{
    public function render()
    {
        return view('livewire.v2.service-basic')
            ->layout('v2.layouts.app');
    }
}
