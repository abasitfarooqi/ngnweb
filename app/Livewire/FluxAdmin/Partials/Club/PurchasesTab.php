<?php

namespace App\Livewire\FluxAdmin\Partials\Club;

use App\Models\ClubMemberPurchase;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class PurchasesTab extends Component
{
    public int $clubMemberId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $purchases = ClubMemberPurchase::with('user')
            ->where('club_member_id', $this->clubMemberId)
            ->orderByDesc('date')
            ->get();

        return view('flux-admin.partials.club.purchases-tab', [
            'purchases' => $purchases,
        ]);
    }
}
