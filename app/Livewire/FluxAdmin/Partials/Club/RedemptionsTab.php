<?php

namespace App\Livewire\FluxAdmin\Partials\Club;

use App\Models\ClubMemberRedeem;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class RedemptionsTab extends Component
{
    public int $clubMemberId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $redemptions = ClubMemberRedeem::with('user')
            ->where('club_member_id', $this->clubMemberId)
            ->orderByDesc('date')
            ->get();

        return view('flux-admin.partials.club.redemptions-tab', [
            'redemptions' => $redemptions,
        ]);
    }
}
