<?php

namespace App\Livewire\Site;

use Livewire\Component;

class ComingSoon extends Component
{
    public function render()
    {
        return view('livewire.site.coming-soon')
            ->layout('components.layouts.public', [
                'title' => 'Coming Soon | NGN Motors',
            ]);
    }
}
