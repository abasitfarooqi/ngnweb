<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\SearchesClubMembers;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\ClubMemberRedeem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Redemption — Flux Admin')]
class RedeemForm extends Component
{
    use SearchesClubMembers;
    use WithAuthorization;

    public ?ClubMemberRedeem $redeem = null;

    public array $form = [];

    public ?float $remainingBalance = null;

    public bool $hasTodayPurchases = false;

    public function mount(?ClubMemberRedeem $redeem = null): void
    {
        $this->resetErrorBag();
        $this->authorizeFullClubAdmin();
        $this->redeem = $redeem?->id ? $redeem : null;

        if ($this->redeem) {
            $this->form = $this->redeem->getAttributes();
            if (! empty($this->form['date'])) {
                $this->form['date'] = Carbon::parse($this->form['date'])->format('Y-m-d');
            }
            $this->form['include_today'] = false;
            $this->fillMemberSearchLabel((int) $this->redeem->club_member_id);
        } else {
            $this->form = [
                'date' => now()->toDateString(),
                'redeem_total' => 0,
                'include_today' => false,
                'branch_id' => '',
            ];
        }

        $this->refreshMemberBalance();
    }

    public function onClubMemberSelected(ClubMember $member): void
    {
        $this->refreshMemberBalance();
    }

    public function updatedFormClubMemberId(): void
    {
        $this->refreshMemberBalance();
    }

    public function updatedFormIncludeToday(): void
    {
        $this->refreshMemberBalance();
    }

    public function save(): void
    {
        $this->validate([
            'form.club_member_id' => ['required', 'integer', 'exists:club_members,id'],
            'form.date' => ['required', 'date'],
            'form.redeem_total' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail): void {
                    $memberId = (int) ($this->form['club_member_id'] ?? 0);
                    $available = $this->availableBalance($memberId, (bool) ($this->form['include_today'] ?? false));
                    if ($memberId && (float) $value > $available) {
                        $fail("The redeem amount (£{$value}) exceeds available balance (£".number_format($available, 2).').');
                    }
                },
            ],
            'form.pos_invoice' => ['nullable', 'string', 'max:120'],
            'form.branch_id' => ['nullable', 'string', 'in:CATFORD,SUTTON,TOOTING'],
            'form.note' => ['nullable', 'string', 'max:255'],
            'form.include_today' => ['boolean'],
        ]);

        $payload = [
            'club_member_id' => $this->form['club_member_id'],
            'date' => $this->form['date'],
            'redeem_total' => $this->form['redeem_total'],
            'pos_invoice' => $this->form['pos_invoice'] ?: null,
            'branch_id' => $this->form['branch_id'] ?: null,
            'note' => $this->form['note'] ?? null,
            'user_id' => backpack_user()?->id ?? auth()->id(),
        ];

        DB::transaction(function () use ($payload): void {
            if ($this->redeem) {
                $this->redeem->update($payload);
                $entry = $this->redeem->refresh();
            } else {
                $entry = ClubMemberRedeem::create($payload);
            }

            $this->applyRedemptionToPurchases($entry);
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Redemption saved.');
        $this->redirect(route('flux-admin.club-redemptions.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.redeem-form');
    }

    protected function refreshMemberBalance(): void
    {
        $memberId = (int) ($this->form['club_member_id'] ?? 0);
        $member = $memberId > 0 ? ClubMember::find($memberId) : null;

        $includeRecent = (bool) ($this->form['include_today'] ?? false);
        $this->remainingBalance = $member
            ? $this->availableBalance((int) $member->id, $includeRecent)
            : null;
        $this->hasTodayPurchases = $member
            ? $member->purchases()->where('date', '>', now()->subHours(16))->exists()
            : false;
    }

    protected function applyRedemptionToPurchases(ClubMemberRedeem $entry): void
    {
        $remaining = round((float) $entry->redeem_total, 2);
        $requested = $remaining;
        $purchaseIds = [];
        $includeRecent = (bool) ($this->form['include_today'] ?? false);

        $this->eligiblePurchases((int) $entry->club_member_id, $includeRecent)
            ->lockForUpdate()
            ->get()
            ->each(function (ClubMemberPurchase $purchase) use (&$remaining, &$purchaseIds): void {
                if ($remaining <= 0.0) {
                    return;
                }

                $discount = round((float) ($purchase->discount ?? 0), 2);
                $alreadyRedeemed = round((float) ($purchase->redeem_amount ?? 0), 2);
                $available = round(max($discount - $alreadyRedeemed, 0), 2);
                $applied = round(min($remaining, $available), 2);

                if ($applied <= 0.0) {
                    return;
                }

                $newRedeemAmount = round($alreadyRedeemed + $applied, 2);
                $purchase->forceFill([
                    'redeem_amount' => $newRedeemAmount,
                    'is_redeemed' => round($discount - $newRedeemAmount, 2) <= 0.01,
                ])->save();

                $remaining = round($remaining - $applied, 2);
                $purchaseIds[] = $purchase->id.' (£'.number_format($applied, 2).')';
            });

        $appliedTotal = round($requested - $remaining, 2);
        $note = trim(($entry->note ?? '')."\nApplied £".number_format($appliedTotal, 2, '.', '').' from purchase IDs: '.implode(', ', $purchaseIds));
        if ($remaining > 0.0) {
            $note .= "\nUnapplied £".number_format($remaining, 2, '.', '').' because no eligible balance remained.';
        }

        $entry->forceFill([
            'redeem_total' => $appliedTotal,
            'note' => trim($note),
        ])->save();
    }

    protected function availableBalance(int $memberId, bool $includeRecent = false): float
    {
        if ($memberId <= 0) {
            return 0.0;
        }

        return round($this->eligiblePurchases($memberId, $includeRecent)->get()->sum(function (ClubMemberPurchase $purchase): float {
            return max(round((float) ($purchase->discount ?? 0) - (float) ($purchase->redeem_amount ?? 0), 2), 0);
        }), 2);
    }

    protected function eligiblePurchases(int $memberId, bool $includeRecent = false)
    {
        $query = ClubMemberPurchase::query()
            ->where('club_member_id', $memberId)
            ->where('date', '>=', now()->subMonths(6))
            ->whereRaw('ROUND(discount - COALESCE(redeem_amount, 0), 2) > 0.01')
            ->orderBy('date')
            ->orderBy('id');

        if (! $includeRecent) {
            $query->where('date', '<=', now()->subHours(16));
        }

        return $query;
    }
}
