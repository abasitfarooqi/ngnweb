<?php

namespace App\Livewire\FluxAdmin\Partials\Club;

use App\Models\ClubMember;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class OverviewTab extends Component
{
    public int $clubMemberId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $member = ClubMember::with('customer', 'partner', 'user')->findOrFail($this->clubMemberId);

        return view('flux-admin.partials.club.overview-tab', [
            'member' => $member,
        ]);
    }
}
