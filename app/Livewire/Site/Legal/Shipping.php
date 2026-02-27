<?php

namespace App\Livewire\Site\Legal;

use Livewire\Component;

class Shipping extends Component
{
    public function render()
    {
        return view('livewire.site.legal.shipping')
            ->layout('components.layouts.public', [
                'title' => 'Shipping Policy | NGN Motors',
            ]);
    }
}
