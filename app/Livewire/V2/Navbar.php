<?php

namespace App\Livewire\V2;

use Livewire\Component;

class Navbar extends Component
{
    public bool $mobileOpen = false;

    public function toggleMobile(): void
    {
        $this->mobileOpen = !$this->mobileOpen;
    }

    public function render()
    {
        return view('livewire.v2.navbar');
    }
}
