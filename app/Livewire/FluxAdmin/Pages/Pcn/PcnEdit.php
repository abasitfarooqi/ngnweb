<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Models\PcnCaseUpdate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class PcnEdit extends Component
{
    use WithAuthorization;

    public PcnCase $pcnCase;

    public array $form = [];

    /** Customer search */
    public string $customerSearch = '';
    public array $customerSuggestions = [];

    /** Motorbike reg search */
    public string $motorbikeSearch = '';
    public array $motorbikeSuggestions = [];

    /** Inline repeatable case updates */
    public array $caseUpdates = [];

    public function mount(PcnCase $pcnCase): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-pcn-portal');
        $this->pcnCase = $pcnCase->load(['updates', 'customer', 'motorbike', 'user']);

        $attrs = $this->pcnCase->getAttributes();
        foreach (['date_of_contravention', 'date_of_letter_issued'] as $f) {
            if (! empty($attrs[$f])) {
                try {
                    $attrs[$f] = \Carbon\Carbon::parse($attrs[$f])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs[$f] = null;
                }
            }
        }
        $this->form = $attrs;

        $this->customerSearch = $this->pcnCase->customer
            ? $this->pcnCase->customer->first_name . ' ' . $this->pcnCase->customer->last_name . ' — ' . $this->pcnCase->customer->email
            : '';

        $this->motorbikeSearch = $this->pcnCase->motorbike?->reg_no ?? '';

        $this->caseUpdates = $this->pcnCase->updates->map(fn ($u) => [
            'id'               => $u->id,
            'update_date'      => $u->update_date ? \Carbon\Carbon::parse($u->update_date)->format('Y-m-d') : null,
            'note'             => $u->note ?? '',
            'additional_fee'   => $u->additional_fee ?? '',
            'is_appealed'      => (bool) $u->is_appealed,
            'is_paid_by_owner' => (bool) $u->is_paid_by_owner,
            'is_paid_by_keeper'=> (bool) $u->is_paid_by_keeper,
            'is_transferred'   => (bool) $u->is_transferred,
            'is_cancled'       => (bool) $u->is_cancled,
        ])->toArray();
    }

    public function getTitle(): string
    {
        return 'Edit PCN ' . $this->pcnCase->pcn_number . ' — Flux Admin';
    }

    public function updatingCustomerSearch(): void
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerSuggestions = [];
            return;
        }
        $this->customerSuggestions = Customer::where(function ($q) {
            $q->where('first_name', 'like', "%{$this->customerSearch}%")
                ->orWhere('last_name', 'like', "%{$this->customerSearch}%")
                ->orWhere('email', 'like', "%{$this->customerSearch}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->customerSearch}%"]);
        })->limit(8)->get(['id', 'first_name', 'last_name', 'email'])->map(fn ($c) => [
            'id'   => $c->id,
            'name' => $c->first_name . ' ' . $c->last_name . ' — ' . $c->email,
        ])->toArray();
    }

    public function selectCustomer(int $id, string $name): void
    {
        $this->form['customer_id']     = $id;
        $this->customerSearch          = $name;
        $this->customerSuggestions     = [];
    }

    public function updatingMotorbikeSearch(): void
    {
        if (strlen($this->motorbikeSearch) < 2) {
            $this->motorbikeSuggestions = [];
            return;
        }
        $this->motorbikeSuggestions = Motorbike::where('reg_no', 'like', "%{$this->motorbikeSearch}%")
            ->limit(8)->get(['id', 'reg_no'])->map(fn ($m) => [
                'id'  => $m->id,
                'reg' => $m->reg_no,
            ])->toArray();
    }

    public function selectMotorbike(int $id, string $reg): void
    {
        $this->form['motorbike_id']    = $id;
        $this->motorbikeSearch         = $reg;
        $this->motorbikeSuggestions    = [];
    }

    public function addCaseUpdate(): void
    {
        $this->caseUpdates[] = [
            'id'               => null,
            'update_date'      => now()->format('Y-m-d'),
            'note'             => '',
            'additional_fee'   => '',
            'is_appealed'      => false,
            'is_paid_by_owner' => false,
            'is_paid_by_keeper'=> false,
            'is_transferred'   => false,
            'is_cancled'       => false,
        ];
    }

    public function removeCaseUpdate(int $index): void
    {
        array_splice($this->caseUpdates, $index, 1);
    }

    public function save(): void
    {
        $this->validate([
            'form.pcn_number'            => ['required', 'string', 'max:100'],
            'form.date_of_contravention' => ['nullable', 'date'],
            'form.time_of_contravention' => ['nullable', 'string', 'max:10'],
            'form.full_amount'           => ['nullable', 'numeric', 'min:0'],
            'form.reduced_amount'        => ['nullable', 'numeric', 'min:0'],
            'form.is_police'             => ['boolean'],
            'form.isClosed'              => ['boolean'],
            'form.council_link'          => ['nullable', 'url', 'max:500'],
            'form.note'                  => ['nullable', 'string', 'max:2000'],
            'form.date_of_letter_issued' => ['nullable', 'date'],
            'form.motorbike_id'          => ['nullable', 'integer'],
            'form.customer_id'           => ['nullable', 'integer'],
        ]);

        $this->pcnCase->update([
            'pcn_number'            => $this->form['pcn_number'] ?? null,
            'date_of_contravention' => $this->form['date_of_contravention'] ?? null,
            'time_of_contravention' => $this->form['time_of_contravention'] ?? null,
            'full_amount'           => $this->form['full_amount'] ?? null,
            'reduced_amount'        => $this->form['reduced_amount'] ?? null,
            'is_police'             => (bool) ($this->form['is_police'] ?? false),
            'isClosed'              => (bool) ($this->form['isClosed'] ?? false),
            'council_link'          => $this->form['council_link'] ?? null,
            'note'                  => $this->form['note'] ?? null,
            'date_of_letter_issued' => $this->form['date_of_letter_issued'] ?? null,
            'motorbike_id'          => $this->form['motorbike_id'] ?? null,
            'customer_id'           => $this->form['customer_id'] ?? null,
        ]);

        foreach ($this->caseUpdates as $upd) {
            if (! empty($upd['id'])) {
                PcnCaseUpdate::where('id', $upd['id'])->update([
                    'update_date'      => $upd['update_date'] ?: null,
                    'note'             => $upd['note'] ?? '',
                    'additional_fee'   => $upd['additional_fee'] ?: null,
                    'is_appealed'      => (bool) ($upd['is_appealed'] ?? false),
                    'is_paid_by_owner' => (bool) ($upd['is_paid_by_owner'] ?? false),
                    'is_paid_by_keeper'=> (bool) ($upd['is_paid_by_keeper'] ?? false),
                    'is_transferred'   => (bool) ($upd['is_transferred'] ?? false),
                    'is_cancled'       => (bool) ($upd['is_cancled'] ?? false),
                ]);
            } else {
                PcnCaseUpdate::create([
                    'case_id'          => $this->pcnCase->id,
                    'user_id'          => auth()->id(),
                    'update_date'      => $upd['update_date'] ?: now(),
                    'note'             => $upd['note'] ?? '',
                    'additional_fee'   => $upd['additional_fee'] ?: null,
                    'is_appealed'      => (bool) ($upd['is_appealed'] ?? false),
                    'is_paid_by_owner' => (bool) ($upd['is_paid_by_owner'] ?? false),
                    'is_paid_by_keeper'=> (bool) ($upd['is_paid_by_keeper'] ?? false),
                    'is_transferred'   => (bool) ($upd['is_transferred'] ?? false),
                    'is_cancled'       => (bool) ($upd['is_cancled'] ?? false),
                ]);
            }
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN case updated.');
        $this->redirect(route('flux-admin.pcn.show', $this->pcnCase), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.edit');
    }
}
