<?php

namespace App\Livewire\Site\Club;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.site.club.dashboard')
            ->layout('components.layouts.public', [
                'title' => 'NGN Club Dashboard | NGN Motors',
            ]);
    }
}
