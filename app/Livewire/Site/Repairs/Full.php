<?php

namespace App\Livewire\Site\Repairs;

use Livewire\Component;

class Full extends Component
{
    public function render()
    {
        return view('livewire.site.repairs.full')
            ->layout('components.layouts.public', [
                'title' => 'Full (Major) Motorcycle Service London | NGN Motors',
                'description' => 'Full major service: engine, transmission, brakes, suspension, electrical, wheels and optional extras. Workshops in Catford, Tooting and Sutton.',
            ]);
    }
}
