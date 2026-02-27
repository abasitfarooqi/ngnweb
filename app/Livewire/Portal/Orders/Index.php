<?php

namespace App\Livewire\Portal\Orders;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.portal.orders.index')
            ->layout('components.layouts.portal');
    }
}
