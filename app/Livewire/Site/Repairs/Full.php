<?php

namespace App\Livewire\Site\Repairs;

use Livewire\Component;

class Full extends Component
{
    public function render()
    {
        return view('livewire.site.repairs.full')
            ->layout('components.layouts.public', [
                'title' => 'Full Motorcycle Service London | From £150 | NGN Motors',
                'description' => 'Comprehensive full motorcycle servicing in London from £150.',
            ]);
    }
}
