<?php

namespace App\Livewire\Portal;

use Livewire\Component;

class Club extends Component
{
    public function render()
    {
        return view('livewire.portal.club')
            ->layout('components.layouts.portal');
    }
}
