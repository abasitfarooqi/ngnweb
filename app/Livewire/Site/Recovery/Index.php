<?php

namespace App\Livewire\Site\Recovery;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.site.recovery.index')
            ->layout('components.layouts.public', [
                'title' => 'Free Motorcycle Recovery in London | NGN Motors',
                'description' => 'Free motorcycle recovery service across London. Fast, reliable collection and delivery. Available 24/7.',
            ]);
    }
}
