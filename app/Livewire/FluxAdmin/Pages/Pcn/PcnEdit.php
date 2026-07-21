<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Models\PcnCaseUpdate;
use App\Services\PcnCaseNotifier;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class PcnEdit extends Component
{
    use WithAuthorization;

    public PcnCase $pcnCase;

    public array $form = [];

    public string $customerSearch = '';

    public array $customerSuggestions = [];

    public string $motorbikeSearch = '';

    public array $motorbikeSuggestions = [];

    public array $caseUpdates = [];

    /** @var array<int, int> */
    public array $removedUpdateIds = [];

    public bool $sendEmail = false;

    public function mount(PcnCase $pcnCase): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-pcns');
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
            ? $this->pcnCase->customer->first_name.' '.$this->pcnCase->customer->last_name.' — '.$this->pcnCase->customer->email
            : '';

        $this->motorbikeSearch = $this->pcnCase->motorbike?->reg_no ?? '';

        $this->caseUpdates = $this->pcnCase->updates->map(fn ($u) => [
            'id' => $u->id,
            'update_date' => $u->update_date ? \Carbon\Carbon::parse($u->update_date)->format('Y-m-d') : null,
            'note' => $u->note ?? '',
            'additional_fee' => $u->additional_fee ?? '',
            'is_appealed' => (bool) $u->is_appealed,
            'is_paid_by_owner' => (bool) $u->is_paid_by_owner,
            'is_paid_by_keeper' => (bool) $u->is_paid_by_keeper,
            'is_transferred' => (bool) $u->is_transferred,
            'is_cancled' => (bool) $u->is_cancled,
        ])->toArray();
    }

    public function getTitle(): string
    {
        return 'Edit PCN '.$this->pcnCase->pcn_number.' — Flux Admin';
    }

    public function getLiabilityLetterProperty(): string
    {
        return <<<'EOT'
Dear Enforcement Team,

Please find attached the required documents for the transfer of liability for the above Penalty Charge Notice, in accordance with:
• Traffic Management Act 2004 (Sections 82–92)
• Road Traffic Act 1988 (Section 66(2) & Schedule 2)
• Road Traffic Offenders Act 1988
• Road Traffic Regulation Act 1984
• Where applicable: London Local Authorities and TfL Acts

At the material time, the vehicle was in the possession and control of the customer identified in the enclosed agreement.

The attached documents meet all statutory requirements for transfer of liability.
Attached documents:
Agreement
Statutory Extract
Authorisation Certificate

Please confirm that liability has been transferred to the customer.
EOT;
    }

    public function updatedCustomerSearch(string $value): void
    {
        if (strlen($value) < 2) {
            $this->customerSuggestions = [];

            return;
        }

        $this->customerSuggestions = Customer::where(function ($q) use ($value) {
            $q->where('first_name', 'like', "%{$value}%")
                ->orWhere('last_name', 'like', "%{$value}%")
                ->orWhere('email', 'like', "%{$value}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$value}%"]);
        })->limit(8)->get(['id', 'first_name', 'last_name', 'email'])->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->first_name.' '.$c->last_name.' — '.$c->email,
        ])->toArray();
    }

    public function selectCustomer(int $id): void
    {
        $customer = Customer::find($id);
        if (! $customer) {
            return;
        }

        $this->form['customer_id'] = $customer->id;
        $this->customerSearch = $customer->first_name.' '.$customer->last_name.' — '.$customer->email;
        $this->customerSuggestions = [];
    }

    public function updatedMotorbikeSearch(string $value): void
    {
        if (! empty($this->form['motorbike_id'])) {
            $selected = Motorbike::find($this->form['motorbike_id']);
            $selectedCompact = strtoupper(preg_replace('/\s+/', '', (string) ($selected?->reg_no ?? '')) ?? '');
            $valueCompact = strtoupper(preg_replace('/\s+/', '', $value) ?? '');
            if ($selected && $selectedCompact !== '' && $selectedCompact === $valueCompact) {
                $this->motorbikeSuggestions = [];

                return;
            }
        }

        $this->form['motorbike_id'] = null;

        if (strlen($value) < 2) {
            $this->motorbikeSuggestions = [];

            return;
        }

        $needle = preg_replace('/\s+/', '', $value);
        $this->motorbikeSuggestions = Motorbike::where(function ($q) use ($value, $needle) {
            $q->where('reg_no', 'like', "%{$value}%")
                ->orWhereRaw("REPLACE(reg_no, ' ', '') LIKE ?", ["%{$needle}%"]);
        })->limit(8)->get(['id', 'reg_no'])->map(fn ($m) => [
            'id' => $m->id,
            'reg' => $m->reg_no,
        ])->toArray();
    }

    public function selectMotorbike(int $id): void
    {
        $motorbike = Motorbike::find($id);
        if (! $motorbike) {
            return;
        }

        $this->form['motorbike_id'] = $motorbike->id;
        $this->motorbikeSearch = $motorbike->reg_no;
        $this->motorbikeSuggestions = [];
    }

    public function commitMotorbikeSearch(): void
    {
        if (! empty($this->form['motorbike_id'])) {
            return;
        }

        if ($this->motorbikeSuggestions === []) {
            $this->updatedMotorbikeSearch($this->motorbikeSearch);
        }

        if ($this->motorbikeSuggestions === []) {
            return;
        }

        $compact = strtoupper(preg_replace('/\s+/', '', $this->motorbikeSearch) ?? '');
        foreach ($this->motorbikeSuggestions as $suggestion) {
            $reg = strtoupper(preg_replace('/\s+/', '', (string) ($suggestion['reg'] ?? '')) ?? '');
            if ($compact !== '' && $reg === $compact) {
                $this->selectMotorbike((int) $suggestion['id']);

                return;
            }
        }

        if (count($this->motorbikeSuggestions) === 1) {
            $this->selectMotorbike((int) $this->motorbikeSuggestions[0]['id']);
        }
    }

    public function addCaseUpdate(): void
    {
        $this->caseUpdates[] = [
            'id' => null,
            'update_date' => now()->format('Y-m-d'),
            'note' => '',
            'additional_fee' => '',
            'is_appealed' => false,
            'is_paid_by_owner' => false,
            'is_paid_by_keeper' => false,
            'is_transferred' => false,
            'is_cancled' => false,
        ];
    }

    public function removeCaseUpdate(int $index): void
    {
        $removed = $this->caseUpdates[$index] ?? null;
        if (! empty($removed['id'])) {
            $this->removedUpdateIds[] = (int) $removed['id'];
        }
        array_splice($this->caseUpdates, $index, 1);
    }

    public function save(PcnCaseNotifier $notifier): void
    {
        $this->commitMotorbikeSearch();

        $this->validate([
            'form.pcn_number' => ['required', 'string', 'max:100'],
            'form.date_of_contravention' => ['required', 'date'],
            'form.time_of_contravention' => ['required', 'string', 'max:10'],
            'form.full_amount' => ['required', 'numeric', 'min:0'],
            'form.reduced_amount' => ['nullable', 'numeric', 'min:0'],
            'form.is_police' => ['boolean'],
            'form.isClosed' => ['boolean'],
            'form.council_link' => ['nullable', 'string', 'max:500'],
            'form.note' => ['nullable', 'string'],
            'form.date_of_letter_issued' => ['nullable', 'date'],
            'form.motorbike_id' => ['required', 'integer', 'exists:motorbikes,id'],
            'form.customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'caseUpdates.*.update_date' => ['required', 'date'],
            'caseUpdates.*.note' => ['required', 'string'],
            'caseUpdates.*.additional_fee' => ['nullable', 'numeric', 'min:0'],
            'sendEmail' => ['boolean'],
        ]);

        $this->pcnCase->update([
            'pcn_number' => $this->form['pcn_number'] ?: null,
            'date_of_contravention' => $this->form['date_of_contravention'] ?: null,
            'time_of_contravention' => $this->form['time_of_contravention'] ?: null,
            'full_amount' => $this->form['full_amount'],
            'reduced_amount' => ($this->form['reduced_amount'] ?? '') !== '' ? $this->form['reduced_amount'] : null,
            'is_police' => (bool) ($this->form['is_police'] ?? false),
            'isClosed' => (bool) ($this->form['isClosed'] ?? false),
            'council_link' => $this->form['council_link'] ?: null,
            'note' => $this->form['note'] ?: null,
            'date_of_letter_issued' => $this->form['date_of_letter_issued'] ?: null,
            'motorbike_id' => $this->form['motorbike_id'] ?: null,
            'customer_id' => $this->form['customer_id'] ?: null,
        ]);

        if ($this->removedUpdateIds !== []) {
            PcnCaseUpdate::where('case_id', $this->pcnCase->id)
                ->whereIn('id', $this->removedUpdateIds)
                ->delete();
            $this->removedUpdateIds = [];
        }

        foreach ($this->caseUpdates as $upd) {
            $payload = [
                'update_date' => $upd['update_date'] ?: null,
                'note' => $upd['note'] ?? '',
                'additional_fee' => ($upd['additional_fee'] ?? '') !== '' ? $upd['additional_fee'] : null,
                'is_appealed' => (bool) ($upd['is_appealed'] ?? false),
                'is_paid_by_owner' => (bool) ($upd['is_paid_by_owner'] ?? false),
                'is_paid_by_keeper' => (bool) ($upd['is_paid_by_keeper'] ?? false),
                'is_transferred' => (bool) ($upd['is_transferred'] ?? false),
                'is_cancled' => (bool) ($upd['is_cancled'] ?? false),
            ];

            if (! empty($upd['id'])) {
                PcnCaseUpdate::where('id', $upd['id'])->where('case_id', $this->pcnCase->id)->update($payload);
            } else {
                PcnCaseUpdate::create(array_merge($payload, [
                    'case_id' => $this->pcnCase->id,
                    'user_id' => auth()->id(),
                    'update_date' => $upd['update_date'] ?: now(),
                ]));
            }
        }

        if ($this->sendEmail) {
            $notifier->sendEmail($this->pcnCase->id);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN case updated.');
        $this->redirect(route('flux-admin.pcn.show', $this->pcnCase), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.edit');
    }
}
