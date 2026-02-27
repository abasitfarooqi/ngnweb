<?php

namespace App\Livewire\Site\Survey;

use Livewire\Component;

class Show extends Component
{
    public $id;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        return view('livewire.site.survey.show')
            ->layout('components.layouts.public', [
                'title' => 'Survey | NGN Motors',
            ]);
    }
}
