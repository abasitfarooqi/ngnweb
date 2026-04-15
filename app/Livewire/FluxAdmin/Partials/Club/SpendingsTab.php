<?php

namespace App\Livewire\FluxAdmin\Partials\Club;

use App\Models\ClubMemberSpending;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class SpendingsTab extends Component
{
    public int $clubMemberId;

    public ?int $expandedSpendingId = null;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function togglePayments(int $spendingId): void
    {
        $this->expandedSpendingId = $this->expandedSpendingId === $spendingId ? null : $spendingId;
    }

    public function render()
    {
        $spendings = ClubMemberSpending::with('user', 'payments', 'payments.user')
            ->where('club_member_id', $this->clubMemberId)
            ->orderByDesc('date')
            ->get();

        return view('flux-admin.partials.club.spendings-tab', [
            'spendings' => $spendings,
        ]);
    }
}
