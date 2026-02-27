<?php

namespace App\Livewire\V2;

use App\Models\Motorbike;
use Livewire\Component;

class RentalDetail extends Component
{
    public int $id;

    public function mount(int $id): void
    {
        $this->id = $id;
    }

    public function render()
    {
        $bike = Motorbike::findOrFail($this->id);

        return view('livewire.v2.rental-detail', compact('bike'))
            ->layout('v2.layouts.app');
    }
}
