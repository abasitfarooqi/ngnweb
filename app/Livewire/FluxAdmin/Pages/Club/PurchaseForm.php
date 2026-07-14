<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\SearchesClubMembers;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Purchase — Flux Admin')]
class PurchaseForm extends Component
{
    use SearchesClubMembers;
    use WithAuthorization;

    public ?ClubMemberPurchase $purchase = null;

    public array $form = [];

    public bool $autoDiscount = true;

    public ?string $posInvoiceWarning = null;

    public function mount(?ClubMemberPurchase $purchase = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-club');
        $this->purchase = $purchase?->id ? $purchase : null;

        if ($this->purchase) {
            $this->form = $this->purchase->getAttributes();
            $this->form['date'] = $this->purchase->date ? Carbon::parse($this->purchase->date)->format('Y-m-d') : null;
            $this->fillMemberSearchLabel((int) $this->purchase->club_member_id);
        } else {
            $this->form = [
                'date' => now()->toDateString(),
                'is_redeemed' => false,
                'percent' => '',
                'total' => '',
                'discount' => '',
                'branch_id' => '',
            ];
        }

        $this->recalculateDiscount();
        $this->checkPosInvoice();
    }

    public function updatedFormPercent(): void
    {
        $this->recalculateDiscount();
    }

    public function updatedFormTotal(): void
    {
        $this->recalculateDiscount();
    }

    public function updatedAutoDiscount(): void
    {
        $this->recalculateDiscount();
    }

    public function updatedFormPosInvoice(): void
    {
        $this->checkPosInvoice();
    }

    public function onClubMemberSelected(ClubMember $member): void
    {
        // balance N/A for purchases
    }

    public function save(): void
    {
        $this->recalculateDiscount();

        $rules = [
            'form.date' => ['required', 'date'],
            'form.club_member_id' => ['required', 'integer', 'exists:club_members,id'],
            'form.pos_invoice' => ['nullable', 'string', 'max:255'],
            'form.percent' => ['required', 'numeric'],
            'form.total' => ['required', 'numeric'],
            'form.discount' => ['required', 'numeric'],
            'form.branch_id' => ['nullable', 'string', 'in:CATFORD,SUTTON,TOOTING'],
            'form.is_redeemed' => ['boolean'],
        ];

        if (filled($this->form['pos_invoice'] ?? null)) {
            $unique = Rule::unique('club_member_purchases', 'pos_invoice');
            if ($this->purchase?->id) {
                $unique = $unique->ignore($this->purchase->id);
            }
            $rules['form.pos_invoice'][] = $unique;
        }

        $this->validate($rules);

        $payload = [
            'date' => $this->form['date'],
            'club_member_id' => $this->form['club_member_id'],
            'pos_invoice' => $this->form['pos_invoice'] ?: null,
            'percent' => $this->form['percent'],
            'total' => $this->form['total'],
            'discount' => $this->form['discount'],
            'branch_id' => $this->form['branch_id'] ?: null,
            'is_redeemed' => (bool) ($this->form['is_redeemed'] ?? false),
            'user_id' => backpack_user()?->id ?? auth()->id(),
        ];

        if ($this->purchase) {
            $this->purchase->update($payload);
        } else {
            ClubMemberPurchase::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Purchase saved.');
        $this->redirect(route('flux-admin.club-purchases.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.purchase-form');
    }

    protected function recalculateDiscount(): void
    {
        if (! $this->autoDiscount) {
            return;
        }

        $percent = is_numeric($this->form['percent'] ?? null) ? (float) $this->form['percent'] : null;
        $total = is_numeric($this->form['total'] ?? null) ? (float) $this->form['total'] : null;

        if ($percent !== null && $total !== null) {
            $this->form['discount'] = number_format(($percent / 100) * $total, 2, '.', '');
        }
    }

    protected function checkPosInvoice(): void
    {
        $invoice = trim((string) ($this->form['pos_invoice'] ?? ''));
        if ($invoice === '') {
            $this->posInvoiceWarning = null;

            return;
        }

        $query = ClubMemberPurchase::where('pos_invoice', $invoice);
        if ($this->purchase?->id) {
            $query->where('id', '!=', $this->purchase->id);
        }

        $this->posInvoiceWarning = $query->exists()
            ? 'This POS invoice already exists on another purchase.'
            : null;
    }
}
