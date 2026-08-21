<?php

namespace App\Livewire\FluxAdmin\Partials\Club;

use App\Models\ClubMemberSpending;
use App\Support\ClubMemberStaffAccess;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class SpendingsTab extends Component
{
    public int $clubMemberId;

    public string $highlightInvoice = '';

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
        $term = trim($this->highlightInvoice);

        $spendings = ClubMemberSpending::with('user', 'payments', 'payments.user')
            ->where('club_member_id', $this->clubMemberId)
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('pos_invoice', 'like', '%'.$term.'%')
                        ->orWhereHas('payments', fn ($pq) => $pq->where('pos_invoice', 'like', '%'.$term.'%'));
                });
            })
            ->orderByDesc('date')
            ->get();

        $expandedId = $this->expandedSpendingId;

        if ($term !== '' && $expandedId === null) {
            $match = $spendings->first(function ($spending) use ($term) {
                if (ClubMemberStaffAccess::invoiceMatches((string) $spending->pos_invoice, $term)) {
                    return true;
                }

                return $spending->payments->contains(
                    fn ($payment) => ClubMemberStaffAccess::invoiceMatches((string) $payment->pos_invoice, $term)
                );
            });

            if ($match) {
                $expandedId = (int) $match->id;
            }
        }

        return view('flux-admin.partials.club.spendings-tab', [
            'spendings' => $spendings,
            'highlightInvoice' => $term,
            'expandedSpendingId' => $expandedId,
        ]);
    }
}
