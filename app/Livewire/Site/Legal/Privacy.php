<?php

namespace App\Livewire\Site\Legal;

use Livewire\Component;

class Privacy extends Component
{
    public function render()
    {
        return view('livewire.site.legal.privacy')
            ->layout('components.layouts.public', [
                'title' => 'Privacy & Cookie Policy | NGN Motors',
            ]);
    }
}
