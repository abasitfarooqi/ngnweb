<?php

namespace App\Livewire\Site;

use Livewire\Component;

class About extends Component
{
    public function render()
    {
        return view('livewire.site.about')
            ->layout('components.layouts.public', [
                'title' => 'About NGN Motors | London Motorcycle Specialists',
                'description' => 'Learn about NGN Motors — London\'s trusted motorcycle specialists since 2018.',
            ]);
    }
}
