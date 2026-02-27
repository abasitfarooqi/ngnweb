<?php

namespace App\Livewire\Site\Shop;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.site.shop.index')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Parts & Accessories Shop | NGN Motors',
                'description' => 'Shop motorcycle parts, accessories, helmets, clothing, and more.',
            ]);
    }
}
