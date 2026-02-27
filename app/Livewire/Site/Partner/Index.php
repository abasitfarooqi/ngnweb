<?php

namespace App\Livewire\Site\Partner;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.site.partner.index')
            ->layout('components.layouts.public', [
                'title' => 'Partner with NGN Motors | Fleet & Trade | London',
                'description' => 'Partner with NGN Motors for fleet rentals, trade accounts and business services.',
            ]);
    }
}
