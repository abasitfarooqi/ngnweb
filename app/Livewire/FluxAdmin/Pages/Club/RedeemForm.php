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
        $this->authorizeModule('see-menu-commons');
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

    public function save(): void
    {
        $this->validate([
            'form.club_member_id' => ['required', 'integer', 'exists:club_members,id'],
            'form.date' => ['required', 'date'],
            'form.redeem_total' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail): void {
                    $member = ClubMember::find($this->form['club_member_id'] ?? null);
                    if ($member && (float) $value > (float) $member->available_redeemable_balance) {
                        $fail("The redeem amount (£{$value}) exceeds available balance (£{$member->available_redeemable_balance}).");
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

        $this->remainingBalance = $member ? (float) $member->available_redeemable_balance : null;
        $this->hasTodayPurchases = $member
            ? $member->purchases()->whereDate('date', now()->toDateString())->exists()
            : false;
    }

    protected function applyRedemptionToPurchases(ClubMemberRedeem $entry): void
    {
        $query = ClubMemberPurchase::query()
            ->where('club_member_id', $entry->club_member_id)
            ->where('is_redeemed', false);

        if (empty($this->form['include_today'])) {
            $query->whereDate('date', '<>', now()->toDateString());
        }

        $purchases = $query->get();

        if ($purchases->isEmpty()) {
            return;
        }

        $totalRedeemed = 0.0;
        foreach ($purchases as $purchase) {
            $redeemValue = (float) ($purchase->discount ?? 0);
            $purchase->forceFill([
                'redeem_amount' => $redeemValue,
                'is_redeemed' => true,
            ])->save();
            $totalRedeemed += $redeemValue;
        }

        $entry->forceFill([
            'redeem_total' => round($totalRedeemed, 2),
            'note' => trim(($entry->note ?? '')."\nRedeemed £".number_format($totalRedeemed, 2, '.', '').' from purchase IDs: '.$purchases->pluck('id')->implode(', ')),
        ])->save();
    }
}
