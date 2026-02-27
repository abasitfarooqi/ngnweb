<?php

namespace App\Livewire\Site\Locations;

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
        return view('livewire.site.locations.index')
            ->layout('components.layouts.public', [
                'title' => 'Our London Locations | Catford, Tooting & Sutton | NGN Motors',
                'description' => 'Find your nearest NGN Motors branch in London. Catford, Tooting and Sutton locations.',
            ]);
    }
}
