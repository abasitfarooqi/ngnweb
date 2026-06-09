<?php

namespace App\Livewire\Site\Mot;

use App\Models\Branch;
use Livewire\Component;

class Index extends Component
{
    public $branches;

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.site.mot.index')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle MOT Booking in London | NGN Motors',
                'description' => 'Quick and reliable motorcycle MOT Booking at our London branches. Book online or call today.',
            ]);
    }
}
