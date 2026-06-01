<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\ClubMemberRedeem;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Redemption — Flux Admin')]
class RedeemForm extends Component
{
    use WithAuthorization;

    public ?ClubMemberRedeem $redeem = null;

    public array $form = [];

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
        } else {
            $this->form = ['date' => now()->toDateString(), 'redeem_total' => 0];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.club_member_id' => ['required', 'integer'],
            'form.date'           => ['required', 'date'],
            'form.redeem_total'   => ['required', 'numeric', 'min:0'],
            'form.pos_invoice'    => ['nullable', 'string', 'max:120'],
            'form.branch_id'      => ['nullable', 'integer', 'exists:branches,id'],
            'form.note'           => ['nullable', 'string'],
        ]);

        $payload = [
            'club_member_id' => $this->form['club_member_id'],
            'date'           => $this->form['date'],
            'redeem_total'   => $this->form['redeem_total'],
            'pos_invoice'    => $this->form['pos_invoice'] ?? null,
            'branch_id'      => $this->form['branch_id'] ?: null,
            'note'           => $this->form['note'] ?? null,
            'user_id'        => auth()->id(),
        ];

        if ($this->redeem) {
            $this->redeem->update($payload);
        } else {
            ClubMemberRedeem::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Redemption saved.');
        $this->redirect(route('flux-admin.club-redemptions.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.club.redeem-form', compact('branches'));
    }
}
