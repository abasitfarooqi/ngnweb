<?php

namespace App\Livewire\Site\Survey;

use Livewire\Component;

class Thanks extends Component
{
    public function render()
    {
        return view('livewire.site.survey.thanks')
            ->layout('components.layouts.public', [
                'title' => 'Thank You | NGN Motors',
            ]);
    }
}
