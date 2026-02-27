<?php

namespace App\Livewire\Site\Shop;

use Livewire\Component;

class Accessories extends Component
{
    public function render()
    {
        return view('livewire.site.shop.accessories')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Accessories London | NGN Motors',
                'description' => 'Browse our range of motorcycle accessories. Helmets, clothing, luggage & more.',
            ]);
    }
}
