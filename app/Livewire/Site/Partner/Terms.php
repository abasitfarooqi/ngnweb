<?php

namespace App\Livewire\Site\Partner;

use Livewire\Component;

class Terms extends Component
{
    public function render()
    {
        return view('livewire.site.partner.terms')
            ->layout('components.layouts.public', [
                'title' => 'Partner Terms & Conditions | NGN Partner Network',
            ]);
    }
}
