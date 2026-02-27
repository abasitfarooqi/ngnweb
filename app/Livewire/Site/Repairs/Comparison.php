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
                'description' => 'Compare our motorcycle service packages at NGN Motors London.',
            ]);
    }
}
