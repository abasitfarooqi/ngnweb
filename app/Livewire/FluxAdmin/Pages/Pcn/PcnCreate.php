<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Models\PcnCaseUpdate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('New PCN Case — Flux Admin')]
class PcnCreate extends Component
{
    use WithAuthorization;

    public array $form = [];

    public string $customerSearch = '';
    public array $customerSuggestions = [];

    public string $motorbikeSearch = '';
    public array $motorbikeSuggestions = [];

    public array $caseUpdates = [];

    public function mount(): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-pcn-portal');

        $this->form = [
            'pcn_number'            => '',
            'date_of_contravention' => '',
            'time_of_contravention' => '',
            'full_amount'           => '',
            'reduced_amount'        => '',
            'is_police'             => false,
            'isClosed'              => false,
            'council_link'          => '',
            'note'                  => '',
            'date_of_letter_issued' => '',
            'motorbike_id'          => null,
            'customer_id'           => null,
        ];
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
        $this->form['customer_id']  = $id;
        $this->customerSearch       = $name;
        $this->customerSuggestions  = [];
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
        $this->form['motorbike_id']  = $id;
        $this->motorbikeSearch       = $reg;
        $this->motorbikeSuggestions  = [];
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

        $pcnCase = PcnCase::create([
            'pcn_number'            => $this->form['pcn_number'],
            'date_of_contravention' => $this->form['date_of_contravention'] ?: null,
            'time_of_contravention' => $this->form['time_of_contravention'] ?: null,
            'full_amount'           => $this->form['full_amount'] ?: null,
            'reduced_amount'        => $this->form['reduced_amount'] ?: null,
            'is_police'             => (bool) ($this->form['is_police'] ?? false),
            'isClosed'              => (bool) ($this->form['isClosed'] ?? false),
            'council_link'          => $this->form['council_link'] ?: null,
            'note'                  => $this->form['note'] ?: null,
            'date_of_letter_issued' => $this->form['date_of_letter_issued'] ?: null,
            'motorbike_id'          => $this->form['motorbike_id'] ?: null,
            'customer_id'           => $this->form['customer_id'] ?: null,
            'user_id'               => auth()->id(),
        ]);

        foreach ($this->caseUpdates as $upd) {
            PcnCaseUpdate::create([
                'case_id'          => $pcnCase->id,
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

        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN case created.');
        $this->redirect(route('flux-admin.pcn.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.create');
    }
}
