<?php

namespace App\Livewire\Site\Partner;

use Livewire\Component;

class Thankyou extends Component
{
    public function render()
    {
        return view('livewire.site.partner.thankyou')
            ->layout('components.layouts.public', [
                'title' => 'Thank You | NGN Partner Network',
            ]);
    }
}
