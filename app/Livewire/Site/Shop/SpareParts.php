<?php

namespace App\Livewire\Site\Shop;

use Livewire\Component;

class SpareParts extends Component
{
    public function render()
    {
        return view('livewire.site.shop.spare-parts')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Spare Parts London | Honda & Yamaha | NGN Motors',
                'description' => 'Genuine motorcycle spare parts in London. Honda & Yamaha parts available.',
            ]);
    }
}
