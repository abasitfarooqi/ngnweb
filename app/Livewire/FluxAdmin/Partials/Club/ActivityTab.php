<?php

namespace App\Livewire\FluxAdmin\Partials\Club;

use App\Models\ClubMember;
use App\Support\ClubMemberStaffAccess;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ActivityTab extends Component
{
    public int $clubMemberId;

    public string $highlightInvoice = '';

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $member = ClubMember::with('purchases.user', 'redemptions.user', 'spendings.user')
            ->findOrFail($this->clubMemberId);

        $timeline = collect();

        $term = trim($this->highlightInvoice);

        foreach ($member->purchases as $p) {
            $invoice = (string) ($p->pos_invoice ?? '');
            $timeline->push([
                'type' => 'Purchase',
                'date' => $p->date ? \Carbon\Carbon::parse($p->date) : null,
                'amount' => $p->total,
                'details' => 'Invoice: ' . ($invoice !== '' ? $invoice : '—') . ' | Discount: £' . number_format($p->discount, 2),
                'user' => $p->user?->first_name,
                'colour' => 'blue',
                'icon' => 'shopping-cart',
                'invoice' => $invoice,
                'matched' => ClubMemberStaffAccess::invoiceMatches($invoice, $term),
            ]);
        }

        foreach ($member->redemptions as $r) {
            $invoice = (string) ($r->pos_invoice ?? '');
            $timeline->push([
                'type' => 'Redemption',
                'date' => $r->date ? \Carbon\Carbon::parse($r->date) : null,
                'amount' => $r->redeem_total,
                'details' => 'Invoice: ' . ($invoice !== '' ? $invoice : '—') . ($r->note ? ' | ' . $r->note : ''),
                'user' => $r->user?->first_name,
                'colour' => 'green',
                'icon' => 'banknotes',
                'invoice' => $invoice,
                'matched' => ClubMemberStaffAccess::invoiceMatches($invoice, $term),
            ]);
        }

        foreach ($member->spendings as $s) {
            $invoice = (string) ($s->pos_invoice ?? '');
            $timeline->push([
                'type' => 'Spending',
                'date' => $s->date,
                'amount' => $s->total,
                'details' => 'Invoice: ' . ($invoice !== '' ? $invoice : '—') . ' | Paid: £' . number_format($s->paid_amount ?? 0, 2),
                'user' => $s->user?->first_name,
                'colour' => 'amber',
                'icon' => 'credit-card',
                'invoice' => $invoice,
                'matched' => ClubMemberStaffAccess::invoiceMatches($invoice, $term),
            ]);
        }

        $timeline = $timeline->sortByDesc('date')->values();
        $searching = $term !== '';

        if ($searching) {
            $timeline = $timeline->where('matched', true)->values();
        }

        return view('flux-admin.partials.club.activity-tab', [
            'timeline' => $timeline,
            'invoiceNotFound' => $searching && $timeline->isEmpty(),
        ]);
    }
}
