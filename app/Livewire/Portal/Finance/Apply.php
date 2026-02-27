<?php

namespace App\Livewire\Portal\Finance;

use Livewire\Component;

class Apply extends Component
{
    public $motorbikeId;

    public function mount($motorbikeId)
    {
        $this->motorbikeId = $motorbikeId;
    }

    public function render()
    {
        return view('livewire.portal.finance.apply')
            ->layout('components.layouts.portal');
    }
}
