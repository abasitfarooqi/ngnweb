<?php

namespace App\Livewire\Site\Shop;

use Livewire\Component;

class GpsTracker extends Component
{
    public function render()
    {
        return view('livewire.site.shop.gps-tracker')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle GPS Trackers London | NGN Motors',
                'description' => 'Protect your motorcycle with a GPS tracker. Professional fitting available at NGN Motors.',
            ]);
    }
}
