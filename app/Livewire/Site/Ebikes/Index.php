<?php

namespace App\Livewire\Site\Ebikes;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.site.ebikes.index')
            ->layout('components.layouts.public', [
                'title' => 'E-Bikes London | Electric Motorcycles | NGN Motors',
                'description' => 'Explore our range of electric bikes and e-scooters at NGN Motors London.',
            ]);
    }
}
