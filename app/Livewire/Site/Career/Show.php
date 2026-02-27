<?php

namespace App\Livewire\Site\Career;

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
        return view('livewire.site.career.show')
            ->layout('components.layouts.public', [
                'title' => 'Job Details | NGN Motors Careers',
            ]);
    }
}
