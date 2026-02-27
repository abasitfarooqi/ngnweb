<?php

namespace App\Livewire\V2;

use Livewire\Component;

class Recovery extends Component
{
    public function render()
    {
        return view('livewire.v2.recovery')
            ->layout('v2.layouts.app');
    }
}
