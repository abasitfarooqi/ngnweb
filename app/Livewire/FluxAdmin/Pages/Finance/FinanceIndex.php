<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Support\AdminDateTimeInput;
use App\Support\FinanceApplicationDeletion;
use App\Support\FluxAdminFinanceListQuery;
use App\Support\FluxAdminFormPayload;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Payment Plan Applications — Flux Admin')]
class FinanceIndex extends Component
{
    use WithCrudForm, WithDataTable, WithExport, WithPagination;

    #[Url(history: true, except: '')]
    public string $contractType = '';

    #[Url(history: true, except: '')]
    public string $status = '';

    public bool $showForm = false;

    #[Url(history: true, except: '')]
    public string $filterLogbook = '';

    #[Url(history: true, except: '1')]
    public string $filterPosted = '1';

    #[Url(history: true, except: '')]
    public string $contractDateFrom = '';

    #[Url(history: true, except: '')]
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

        if (! in_array($this->filterPosted, ['0', '1'], true)) {
            $this->filterPosted = '1';
        }
    }

    public function resetFinanceFilters(): void
    {
        $this->contractType = '';
        $this->status = '';
        $this->filterLogbook = '';
        $this->filterPosted = '1';
        $this->contractDateFrom = '';
        $this->contractDateTo = '';
        $this->search = '';
        $this->sortField = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 20;
        $this->resetPage();
    }

    public function hasActiveFinanceFilters(): bool
    {
        return $this->search !== ''
            || $this->contractType !== ''
            || $this->status !== ''
            || $this->filterLogbook !== ''
            || $this->filterPosted !== '1'
            || $this->contractDateFrom !== ''
            || $this->contractDateTo !== '';
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
        if (empty($attributes['user_id'])) {
            $attributes['user_id'] = FluxAdminFormPayload::adminUserId();
        }

        $attributes['is_new'] = false;
        $attributes['is_used'] = false;
        $attributes['is_used_extended'] = false;
        $attributes['is_used_extended_custom'] = false;
        $attributes['is_new_latest'] = (bool) ($attributes['is_new_latest'] ?? false);
        $attributes['is_used_latest'] = (bool) ($attributes['is_used_latest'] ?? false);

        if (empty($attributes['is_subscription'])) {
            $attributes['subscription_option'] = null;
        }
        if (empty($attributes['is_subscription']) && empty($attributes['is_new_latest']) && empty($attributes['is_used_latest'])) {
            $attributes['subs_payment_date'] = null;
        }

        if (! empty($attributes['contract_date'])) {
            $attributes['contract_date'] = AdminDateTimeInput::fromLocal((string) $attributes['contract_date'])
                ?? now()->format('Y-m-d H:i:s');
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
            'contract_date'         => AdminDateTimeInput::toLocal(now()),
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
                $this->formData[$field] = $field === 'contract_date'
                    ? AdminDateTimeInput::toLocal($this->formData[$field])
                    : \Carbon\Carbon::parse($this->formData[$field])->format('Y-m-d');
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
        $application = FinanceApplication::findOrFail($id);

        try {
            FinanceApplicationDeletion::delete($application);
        } catch (\RuntimeException $e) {
            $this->dispatch('flux-admin:toast', type: 'error', message: $e->getMessage());

            return;
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Application deleted.');
    }

    public function setContractType(string $type): void
    {
        $allowed = ['is_new_latest', 'is_used_latest'];
        if (! in_array($type, $allowed, true)) {
            return;
        }

        $this->formData['is_new'] = false;
        $this->formData['is_used'] = false;
        $this->formData['is_used_extended'] = false;
        $this->formData['is_used_extended_custom'] = false;
        $this->formData['is_new_latest'] = ($type === 'is_new_latest');
        $this->formData['is_used_latest'] = ($type === 'is_used_latest');
    }

    protected function buildQuery(): Builder
    {
        $query = FinanceApplication::with([
            'customer',
            'items.motorbike:id,reg_no,make,model',
        ]);

        if ($this->search !== '') {
            $term = trim($this->search);
            $like = '%'.$term.'%';
            $query->where(function ($q) use ($like, $term) {
                $q->where('id', 'like', $like)
                    ->orWhereHas('customer', function ($cq) use ($like) {
                        $cq->where('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like)
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$like]);
                    })
                    ->orWhereHas('items.motorbike', function ($mq) use ($like, $term) {
                        $mq->where('reg_no', 'like', $like);
                        $needle = preg_replace('/\s+/', '', $term) ?? '';
                        if ($needle !== '') {
                            $mq->orWhereRaw("REPLACE(reg_no, ' ', '') LIKE ?", ['%'.$needle.'%']);
                        }
                    });
            });
        }

        if ($this->contractType !== '') {
            $query->where($this->contractType, true);
        }

        if ($this->status === 'active') {
            $query->activePaymentPlan();
        } elseif ($this->status === 'cancelled') {
            $query->where('is_cancelled', true);
        }

        $query
            ->when($this->filterLogbook !== '', fn ($q) => $q->where('log_book_sent', $this->filterLogbook === '1'))
            ->where('is_posted', $this->filterPosted === '1')
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
            'Contract date'       => fn ($r) => $r->contract_date ? \Carbon\Carbon::parse($r->contract_date)->format('d M Y H:i') : '',
            'First instalment'    => fn ($r) => $r->first_instalment_date ? \Carbon\Carbon::parse($r->first_instalment_date)->format('d M Y') : '',
            'Posted'              => fn ($r) => $r->is_posted ? 'Yes' : 'No',
            'Log book sent'       => fn ($r) => $r->log_book_sent ? 'Yes' : 'No',
            'Status'              => fn ($r) => match (true) {
                (bool) $r->is_cancelled => 'Cancelled',
                (bool) $r->log_book_sent || $r->logbook_transfer_date !== null => 'Completed',
                default => 'Active',
            },
            'Notes'               => 'notes',
        ];
    }

    public function listCountLabel(): string
    {
        return $this->filterPosted === '0'
            ? 'not posted applications'
            : 'posted applications';
    }

    public function render()
    {
        $applications = $this->buildQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.finance.index', [
            'applications' => $applications,
            'listCountLabel' => $this->listCountLabel(),
        ]);
    }
}
