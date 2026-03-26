<?php

namespace App\Livewire\Site\Club;

use Livewire\Component;

class Terms extends Component
{
    public function render()
    {
        return view('livewire.site.club.terms')
            ->layout('components.layouts.public', [
                'title' => 'NGN Club Terms & Conditions | NGN Motors',
                'description' => 'NGN Club loyalty programme terms and conditions.',
            ]);
    }
}
