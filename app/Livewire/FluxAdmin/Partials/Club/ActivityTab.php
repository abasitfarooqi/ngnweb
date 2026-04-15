<?php

namespace App\Livewire\FluxAdmin\Partials\Club;

use App\Models\ClubMember;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ActivityTab extends Component
{
    public int $clubMemberId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $member = ClubMember::with('purchases.user', 'redemptions.user', 'spendings.user')
            ->findOrFail($this->clubMemberId);

        $timeline = collect();

        foreach ($member->purchases as $p) {
            $timeline->push([
                'type' => 'Purchase',
                'date' => $p->date ? \Carbon\Carbon::parse($p->date) : null,
                'amount' => $p->total,
                'details' => 'Invoice: ' . ($p->pos_invoice ?? '—') . ' | Discount: £' . number_format($p->discount, 2),
                'user' => $p->user?->first_name,
                'colour' => 'blue',
                'icon' => 'shopping-cart',
            ]);
        }

        foreach ($member->redemptions as $r) {
            $timeline->push([
                'type' => 'Redemption',
                'date' => $r->date ? \Carbon\Carbon::parse($r->date) : null,
                'amount' => $r->redeem_total,
                'details' => 'Invoice: ' . ($r->pos_invoice ?? '—') . ($r->note ? ' | ' . $r->note : ''),
                'user' => $r->user?->first_name,
                'colour' => 'green',
                'icon' => 'banknotes',
            ]);
        }

        foreach ($member->spendings as $s) {
            $timeline->push([
                'type' => 'Spending',
                'date' => $s->date,
                'amount' => $s->total,
                'details' => 'Invoice: ' . ($s->pos_invoice ?? '—') . ' | Paid: £' . number_format($s->paid_amount ?? 0, 2),
                'user' => $s->user?->first_name,
                'colour' => 'amber',
                'icon' => 'credit-card',
            ]);
        }

        $timeline = $timeline->sortByDesc('date')->values();

        return view('flux-admin.partials.club.activity-tab', [
            'timeline' => $timeline,
        ]);
    }
}
