<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Models\ClubMember;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ClubShow extends Component
{
    public ClubMember $clubMember;

    public string $activeTab = 'overview';

    public function mount(ClubMember $clubMember): void
    {
        $this->clubMember = $clubMember->load('customer', 'partner', 'user', 'purchases', 'redemptions', 'spendings');
    }

    public function getTitle(): string
    {
        return $this->clubMember->full_name . ' — Flux Admin';
    }

    public function render()
    {
        return view('flux-admin.pages.club.show');
    }
}
