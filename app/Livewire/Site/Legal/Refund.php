<?php

namespace App\Livewire\Site\Legal;

use Livewire\Component;

class Refund extends Component
{
    public function render()
    {
        return view('livewire.site.legal.refund')
            ->layout('components.layouts.public', [
                'title' => 'Refund & Returns Policy | NGN Motors',
            ]);
    }
}
