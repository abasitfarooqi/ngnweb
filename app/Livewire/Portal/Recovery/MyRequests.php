<?php

namespace App\Livewire\Portal\Recovery;

use Livewire\Component;

class MyRequests extends Component
{
    public function render()
    {
        return view('livewire.portal.recovery.my-requests')
            ->layout('components.layouts.portal');
    }
}
