<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Customer;
use App\Models\FinanceApplication;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Finance Applications — Flux Admin')]
class FinanceIndex extends Component
{
    use WithCrudForm, WithDataTable, WithExport, WithPagination;

    public string $contractType = '';

    public string $status = '';

    public bool $showForm = false;

    public string $filterLogbook = '';

    public string $filterPosted = '';

    public string $contractDateFrom = '';

    public string $contractDateTo = '';

    // Customer search autocomplete
    public string $customerSearch = '';

    public array $customerSuggestions = [];

    public ?int $selectedCustomerId = null;

    public string $selectedCustomerName = '';

    public function mount(): void
    {
        $this->exportFilename = 'finance-applications';
        $this->exportable = true;
    }

    public function updatingContractType(): void { $this->resetPage(); }

    public function updatingStatus(): void { $this->resetPage(); }

    public function updatingFilterLogbook(): void { $this->resetPage(); }

    public function updatingFilterPosted(): void { $this->resetPage(); }

    public function updatingContractDateFrom(): void { $this->resetPage(); }

    public function updatingContractDateTo(): void { $this->resetPage(); }

    public function updatingCustomerSearch(): void
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerSuggestions = [];

            return;
        }

        $this->customerSuggestions = Customer::query()
            ->where(function ($q) {
                $q->where('first_name', 'like', '%'.$this->customerSearch.'%')
                    ->orWhere('last_name', 'like', '%'.$this->customerSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->customerSearch.'%')
                    ->orWhere('phone', 'like', '%'.$this->customerSearch.'%');
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone'])
            ->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->first_name.' '.$c->last_name,
                'sub'  => $c->email.' · '.$c->phone,
            ])
            ->toArray();
    }

    public function selectCustomer(int $id, string $name): void
    {
        $this->selectedCustomerId = $id;
        $this->selectedCustomerName = $name;
        $this->formData['customer_id'] = $id;
        $this->customerSearch = $name;
        $this->customerSuggestions = [];
    }

    protected function formModel(): string { return FinanceApplication::class; }

    protected function formRules(): array
    {
        return [
            'formData.customer_id'           => ['required', 'integer', 'exists:customers,id'],
            'formData.contract_date'          => ['nullable', 'date'],
            'formData.first_instalment_date'  => ['nullable', 'date'],
            'formData.motorbike_price'        => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'formData.weekly_instalment'      => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'formData.deposit'                => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'formData.extra'                  => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'formData.extra_items'            => ['nullable', 'string'],
            'formData.notes'                  => ['nullable', 'string'],
            'formData.is_monthly'             => ['boolean'],
            'formData.is_new'                 => ['boolean'],
            'formData.is_used'                => ['boolean'],
            'formData.is_new_latest'          => ['boolean'],
            'formData.is_used_latest'         => ['boolean'],
            'formData.is_used_extended'       => ['boolean'],
            'formData.is_used_extended_custom' => ['boolean'],
            'formData.is_subscription'        => ['boolean'],
            'formData.subscription_option'    => ['nullable', \Illuminate\Validation\Rule::in(['A', 'B', 'C', 'D'])],
            'formData.subs_payment_date'      => ['nullable', 'integer', 'min:1', 'max:31'],
            'formData.is_posted'              => ['boolean'],
            'formData.is_cancelled'           => ['boolean'],
            'formData.reason_of_cancellation' => ['nullable', 'string'],
            'formData.log_book_sent'          => ['boolean'],
        ];
    }

    protected function beforeSave(array $attributes): array
    {
        // Only one contract type can be true at once; handled by the form toggle
        if (empty($attributes['user_id'])) {
            $attributes['user_id'] = backpack_user()?->id;
        }
        if (empty($attributes['is_subscription'])) {
            $attributes['subscription_option'] = null;
        }
        if (empty($attributes['is_subscription']) && empty($attributes['is_new_latest']) && empty($attributes['is_used_latest'])) {
            $attributes['subs_payment_date'] = null;
        }

        return $attributes;
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->customerSearch = '';
        $this->customerSuggestions = [];
        $this->selectedCustomerId = null;
        $this->selectedCustomerName = '';
        $this->formData = [
            'contract_date'         => now()->format('Y-m-d'),
            'first_instalment_date' => now()->addDays(7 - date('N') + 5)->format('Y-m-d'),
            'is_new'                => false,
            'is_used'               => false,
            'is_new_latest'         => false,
            'is_used_latest'        => false,
            'is_used_extended'      => false,
            'is_used_extended_custom' => false,
            'is_subscription'       => false,
            'subscription_option'   => null,
            'subs_payment_date'     => null,
            'is_monthly'            => false,
            'is_posted'             => false,
            'is_cancelled'          => false,
            'log_book_sent'         => false,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $app = FinanceApplication::with('customer')->findOrFail($id);
        $this->fillFromModel($app);
        $this->selectedCustomerId = $app->customer_id;
        $this->selectedCustomerName = $app->customer ? $app->customer->first_name.' '.$app->customer->last_name : '';
        $this->customerSearch = $this->selectedCustomerName;
        $this->customerSuggestions = [];
        foreach (['contract_date', 'first_instalment_date', 'logbook_transfer_date', 'cancelled_at'] as $field) {
            if (! empty($this->formData[$field])) {
                $this->formData[$field] = \Carbon\Carbon::parse($this->formData[$field])->format('Y-m-d');
            }
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Finance application saved.');
    }

    public function delete(int $id): void
    {
        FinanceApplication::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Application deleted.');
    }

    public function setContractType(string $type): void
    {
        $all = ['is_new', 'is_used', 'is_new_latest', 'is_used_latest', 'is_used_extended', 'is_used_extended_custom', 'is_subscription'];
        foreach ($all as $t) {
            $this->formData[$t] = ($t === $type);
        }
        if ($type !== 'is_subscription') {
            $this->formData['subscription_option'] = null;
        } elseif (empty($this->formData['subscription_option'])) {
            $this->formData['subscription_option'] = 'A';
        }
        if (! in_array($type, ['is_subscription', 'is_new_latest', 'is_used_latest'], true)) {
            $this->formData['subs_payment_date'] = null;
        }
    }

    protected function buildQuery(): Builder
    {
        $query = FinanceApplication::with('customer', 'user')->withCount('items');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', function ($cq) {
                        $cq->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->contractType !== '') {
            $query->where($this->contractType, true);
        }

        if ($this->status === 'active') {
            $query->where(function ($q) {
                $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
            });
        } elseif ($this->status === 'cancelled') {
            $query->where('is_cancelled', true);
        }

        $query
            ->when($this->filterLogbook !== '', fn ($q) => $q->where('log_book_sent', $this->filterLogbook === '1'))
            ->when($this->filterPosted !== '', fn ($q) => $q->where('is_posted', $this->filterPosted === '1'))
            ->when($this->contractDateFrom !== '', fn ($q) => $q->whereDate('contract_date', '>=', $this->contractDateFrom))
            ->when($this->contractDateTo !== '', fn ($q) => $q->whereDate('contract_date', '<=', $this->contractDateTo));

        return $query;
    }

    protected function exportQuery(): Builder
    {
        return $this->buildQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID'                  => 'id',
            'Customer'            => fn ($r) => $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '',
            'Contract type'       => fn ($r) => match (true) {
                (bool) $r->is_new                   => 'New Motorcycle',
                (bool) $r->is_new_latest && (bool) $r->is_subscription => 'New Latest + Subscription',
                (bool) $r->is_used_latest && (bool) $r->is_subscription => 'Used Latest + Subscription',
                (bool) $r->is_subscription        => 'Subscription',
                (bool) $r->is_new_latest          => 'New Latest',
                (bool) $r->is_used_latest         => 'Used Latest',
                (bool) $r->is_used_extended_custom => 'Used Ext Custom',
                (bool) $r->is_used_extended       => 'Used Extended',
                (bool) $r->is_used                => 'Used',
                default                           => 'Unknown',
            },
            'Deposit'             => 'deposit',
            'Monthly instalment'  => 'weekly_instalment',
            'Contract date'       => fn ($r) => $r->contract_date ? \Carbon\Carbon::parse($r->contract_date)->format('d M Y') : '',
            'First instalment'    => fn ($r) => $r->first_instalment_date ? \Carbon\Carbon::parse($r->first_instalment_date)->format('d M Y') : '',
            'Posted'              => fn ($r) => $r->is_posted ? 'Yes' : 'No',
            'Log book sent'       => fn ($r) => $r->log_book_sent ? 'Yes' : 'No',
            'Status'              => fn ($r) => $r->is_cancelled ? 'Cancelled' : 'Active',
            'Notes'               => 'notes',
        ];
    }

    public function render()
    {
        $applications = $this->buildQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.finance.index', compact('applications'));
    }
}
