<?php

namespace App\Livewire\Site\Repairs;

use Livewire\Component;

class Basic extends Component
{
    public function render()
    {
        return view('livewire.site.repairs.basic')
            ->layout('components.layouts.public', [
                'title' => 'Basic Motorcycle Service London | From £80 | NGN Motors',
                'description' => 'Professional basic motorcycle servicing in London from £80.',
            ]);
    }
}
