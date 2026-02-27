<?php

namespace App\Livewire\V2;

use Livewire\Component;

class About extends Component
{
    public function render()
    {
        return view('livewire.v2.about')
            ->layout('v2.layouts.app');
    }
}
