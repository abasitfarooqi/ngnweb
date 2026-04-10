<?php

namespace App\Livewire\Portal\Repairs;

use Livewire\Component;

class Request extends Component
{
    public function render()
    {
        return view('livewire.portal.repairs.request')
            ->layout('components.layouts.portal', ['title' => 'Repair Enquiry | My Account']);
    }
}
