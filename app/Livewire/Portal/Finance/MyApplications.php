<?php

namespace App\Livewire\Portal\Finance;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyApplications extends Component
{
    public function render()
    {
        return view('livewire.portal.finance.my-applications')
            ->layout('components.layouts.portal');
    }
}
