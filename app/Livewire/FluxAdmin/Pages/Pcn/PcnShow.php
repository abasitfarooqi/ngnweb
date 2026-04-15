<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Models\PcnCase;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class PcnShow extends Component
{
    public PcnCase $pcnCase;

    public string $activeTab = 'details';

    public function mount(PcnCase $pcnCase): void
    {
        $this->pcnCase = $pcnCase->load('customer', 'motorbike', 'user');
    }

    public function getTitle(): string
    {
        return 'PCN ' . $this->pcnCase->pcn_number . ' — Flux Admin';
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.show');
    }
}
