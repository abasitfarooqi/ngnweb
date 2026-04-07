<?php

namespace App\Livewire\Site\Repairs;

use Livewire\Component;

class Comparison extends Component
{
    public function render()
    {
        return view('livewire.site.repairs.comparison')
            ->layout('components.layouts.public', [
                'title' => 'Service Comparison | Basic vs Full Service | NGN Motors',
                'description' => "Compare NGN's basic and full motorcycle service packages (included vs not included) to pick the right maintenance for your bike. Catford, Sutton and Tooting.",
            ]);
    }
}
