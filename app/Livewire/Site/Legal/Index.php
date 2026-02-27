<?php

namespace App\Livewire\Site\Legal;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.site.legal.index')
            ->layout('components.layouts.public', [
                'title' => 'Legal Information | NGN Motors',
            ]);
    }
}
