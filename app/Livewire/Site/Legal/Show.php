<?php

namespace App\Livewire\Site\Legal;

use Livewire\Component;

class Show extends Component
{
    public $slug;

    public function mount($slug = 'terms')
    {
        $this->slug = $slug;
    }

    public function render()
    {
        return view('livewire.site.legal.show')
            ->layout('components.layouts.public', [
                'title' => 'Terms & Conditions | NGN Motors',
            ]);
    }
}
