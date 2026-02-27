<?php

namespace App\Livewire\Site\Repairs;

use Livewire\Component;

class RepairServices extends Component
{
    public function render()
    {
        return view('livewire.site.repairs.repair-services')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Repair Services London | Expert Mechanics | NGN Motors',
                'description' => 'Professional motorcycle repairs in London. Expert mechanics at NGN Motors.',
            ]);
    }
}
