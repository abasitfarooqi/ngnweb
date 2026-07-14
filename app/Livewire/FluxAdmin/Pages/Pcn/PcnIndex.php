<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Exports\PcnCaseExport;
use App\Exports\PcnCaseWithUpdatesExport;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Models\PcnCaseUpdate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN Cases — Flux Admin')]
class PcnIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public string $status = '';

    public string $isPolice = '';

    public string $filterDateFrom = '';

    public string $filterDateTo = '';

    public string $filterEverAppealed = '';

    public string $filterUpdateStatus = '';

    /** @var array<int, array<string, mixed>> Inline repeatable case updates */
    public array $caseUpdates = [];

    /** Customer search for PCN form */
    public string $customerSearch = '';

    /** @var array<int, array<string, mixed>> */
    public array $customerSuggestions = [];

    public function mount(): void
    {
        $this->authorizeModule('see-menu-pcns');
        $this->sortField = 'date_of_contravention';
        $this->sortDirection = 'desc';

        // Honour Backpack-style query links from the overview dashboard.
        if (request()->filled('isClosed')) {
            $this->status = request('isClosed') === '0' || request('isClosed') === 'false' ? 'open' : 'closed';
        }
        if (request()->filled('has_been_appealed')) {
            $this->filterEverAppealed = request('has_been_appealed') ? '1' : '0';
        }
    }

    protected function formModel(): string { return PcnCase::class; }

    protected function formRules(): array
    {
        return [
            'formData.pcn_number'            => ['required', 'string', 'max:100'],
            'formData.date_of_contravention' => ['required', 'date'],
            'formData.time_of_contravention' => ['required', 'string', 'max:10'],
            'formData.full_amount'           => ['required', 'numeric', 'min:0'],
            'formData.reduced_amount'        => ['nullable', 'numeric', 'min:0'],
            'formData.is_police'             => ['boolean'],
            'formData.isClosed'              => ['boolean'],
            'formData.council_link'          => ['nullable', 'string', 'max:500'],
            'formData.note'                  => ['nullable', 'string', 'max:2000'],
            'formData.date_of_letter_issued' => ['nullable', 'date'],
            'formData.motorbike_id'          => ['required', 'integer', 'exists:motorbikes,id'],
            'formData.customer_id'           => ['nullable', 'integer', 'exists:customers,id'],
        ];
    }

    /** Motorbike reg search for PCN form */
    public string $motorbikeSearch = '';

    /** @var array<int, array<string, mixed>> */
    public array $motorbikeSuggestions = [];

    public function updatedMotorbikeSearch(string $value): void
    {
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

    public function selectPcnMotorbike(int $id): void
    {
        $motorbike = Motorbike::find($id);
        if (! $motorbike) {
            return;
        }

        $this->formData['motorbike_id'] = $motorbike->id;
        $this->motorbikeSearch = $motorbike->reg_no;
        $this->motorbikeSuggestions = [];
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

    public function selectPcnCustomer(int $id): void
    {
        $customer = Customer::find($id);
        if (! $customer) {
            return;
        }

        $this->formData['customer_id'] = $customer->id;
        $this->customerSearch = $customer->first_name.' '.$customer->last_name.' — '.$customer->email;
        $this->customerSuggestions = [];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId            = null;
        $this->formData            = ['is_police' => false, 'isClosed' => false];
        $this->caseUpdates         = [];
        $this->customerSearch      = '';
        $this->customerSuggestions = [];
        $this->motorbikeSearch     = '';
        $this->motorbikeSuggestions = [];
        $this->showForm            = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $pcn = PcnCase::with(['updates', 'customer', 'motorbike'])->findOrFail($id);
        $this->fillFromModel($pcn);
        // Format date fields for HTML date inputs
        foreach (['date_of_contravention', 'date_of_letter_issued'] as $field) {
            if (! empty($this->formData[$field])) {
                try {
                    $this->formData[$field] = \Carbon\Carbon::parse($this->formData[$field])->format('Y-m-d');
                } catch (\Throwable) {
                    $this->formData[$field] = null;
                }
            }
        }
        $this->customerSearch      = $pcn->customer ? $pcn->customer->first_name . ' ' . $pcn->customer->last_name . ' — ' . $pcn->customer->email : '';
        $this->customerSuggestions = [];
        $this->motorbikeSearch     = $pcn->motorbike?->reg_no ?? '';
        $this->motorbikeSuggestions = [];
        $this->caseUpdates         = $pcn->updates->map(fn ($u) => [
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
        $this->showForm = true;
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

    public function saveForm(): void
    {
        $this->formData['is_police'] = (bool) ($this->formData['is_police'] ?? false);
        $this->formData['isClosed']  = (bool) ($this->formData['isClosed'] ?? false);
        $savedModel = $this->save();
        $pcnId = $savedModel->id;

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
                    'case_id'          => $pcnId,
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

        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN case saved.');
    }

    public function delete(int $id): void
    {
        PcnCase::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN case deleted.');
    }

    public function exportSummary()
    {
        return Excel::download(new PcnCaseExport, 'pcn_cases_summary.xlsx');
    }

    public function exportWithUpdates()
    {
        return Excel::download(new PcnCaseWithUpdatesExport, 'pcn_cases_with_updates.xlsx');
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingIsPolice(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEverAppealed(): void
    {
        $this->resetPage();
    }

    public function updatingFilterUpdateStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PcnCase::with(['customer', 'motorbike', 'user', 'updates' => fn ($q) => $q->select('id', 'case_id', 'is_appealed')]);

        if ($this->search !== '') {
            $term = $this->search;
            $query->where(function ($q) use ($term) {
                $q->where('pcn_number', 'like', "%{$term}%")
                    ->orWhere('note', 'like', "%{$term}%")
                    ->orWhereHas('customer', function ($cq) use ($term) {
                        $cq->where('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    })
                    ->orWhereHas('motorbike', function ($mq) use ($term) {
                        $mq->where('reg_no', 'like', "%{$term}%");
                    });
            });
        }

        if ($this->status === 'open') {
            $query->where('isClosed', false);
        } elseif ($this->status === 'closed') {
            $query->where('isClosed', true);
        }

        if ($this->isPolice === 'yes') {
            $query->where('is_police', true);
        } elseif ($this->isPolice === 'no') {
            $query->where(function ($q) {
                $q->where('is_police', false)->orWhereNull('is_police');
            });
        }

        if ($this->filterDateFrom !== '') {
            $query->whereDate('date_of_contravention', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo !== '') {
            $query->whereDate('date_of_contravention', '<=', $this->filterDateTo);
        }

        if ($this->filterEverAppealed === '1') {
            $query->whereHas('updates', fn ($q) => $q->where('is_appealed', true));
        } elseif ($this->filterEverAppealed === '0') {
            $query->whereDoesntHave('updates', fn ($q) => $q->where('is_appealed', true));
        }

        if ($this->filterUpdateStatus === 'cancelled') {
            $query->whereHas('updates', fn ($q) => $q->where('is_cancled', true));
        } elseif ($this->filterUpdateStatus === 'transferred') {
            $query->whereHas('updates', fn ($q) => $q->where('is_transferred', true));
        }

        $cases = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.pcn.index', [
            'cases' => $cases,
        ]);
    }
}
