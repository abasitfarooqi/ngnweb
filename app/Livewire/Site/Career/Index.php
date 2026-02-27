<?php

namespace App\Livewire\Site\Career;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.site.career.index')
            ->layout('components.layouts.public', [
                'title' => 'Careers at NGN Motors | Jobs in London',
                'description' => 'Join the NGN Motors team. Motorcycle mechanics, sales, and customer service roles in London.',
            ]);
    }
}
