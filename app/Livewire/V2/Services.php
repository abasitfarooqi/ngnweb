<?php

namespace App\Livewire\V2;

use Livewire\Component;

class Services extends Component
{
    public function render()
    {
        return view('livewire.v2.services')
            ->layout('v2.layouts.app');
    }
}
