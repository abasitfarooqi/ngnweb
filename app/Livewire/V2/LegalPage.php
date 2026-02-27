<?php

namespace App\Livewire\V2;

use Livewire\Component;

class LegalPage extends Component
{
    public string $page = 'privacy';

    public function mount(string $page = 'privacy'): void
    {
        $this->page = $page;
    }

    public function render()
    {
        return view('livewire.v2.legal-page', ['page' => $this->page])
            ->layout('v2.layouts.app');
    }
}
