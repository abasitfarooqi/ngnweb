<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Http\Controllers\Admin\FinanceApplicationCrudController;
use App\Models\ApplicationItem;
use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class FinanceForm extends Component
{
    use WithAuthorization;

    public ?FinanceApplication $application = null;

    public array $form = [];

    public string $customerSearch = '';

    public array $customerSuggestions = [];

    public array $itemRows = [];

    public array $motorbikeSearches = [];

    public array $motorbikeSuggestions = [];

    public function mount(?FinanceApplication $application = null): void
    {
        $this->resetErrorBag();
        $this->application = $application;

        if ($application && $application->exists) {
            $application->load('customer', 'items.motorbike');
            $attrs = $application->getAttributes();
            foreach (['contract_date', 'first_instalment_date', 'logbook_transfer_date', 'cancelled_at'] as $field) {
                if (! empty($attrs[$field])) {
                    try {
                        $attrs[$field] = \Carbon\Carbon::parse($attrs[$field])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$field] = null;
                    }
                }
            }
            $attrs['contract_type'] = $this->resolveContractType($application);
            $attrs['insurance_pcn'] = true;
            $attrs['no_email'] = true;
            $this->form = $attrs;
            $this->customerSearch = $application->customer
                ? $application->customer->first_name . ' ' . $application->customer->last_name
                : '';
            $this->itemRows = $application->items
                ->map(fn (ApplicationItem $item) => [
                    'id' => $item->id,
                    'motorbike_id' => $item->motorbike_id,
                    'is_posted' => (bool) $item->is_posted,
                    'user_id' => $item->user_id,
                ])
                ->values()
                ->toArray();
            foreach ($application->items as $index => $item) {
                $this->motorbikeSearches[$index] = $item->motorbike?->detail ?? '';
            }
        } else {
            $nextFriday = now()->addDays(7 - (int) date('N') + 5);
            if ((int) date('N') >= 5) {
                $nextFriday = now()->addDays(12 - (int) date('N'));
            }

            $this->form = [
                'contract_date'           => now()->format('Y-m-d'),
                'first_instalment_date'   => $nextFriday->format('Y-m-d'),
                'is_new'                  => false,
                'is_used'                 => false,
                'is_used_extended'        => false,
                'is_used_extended_custom' => false,
                'is_new_latest'           => true,
                'is_used_latest'          => false,
                'is_subscription'         => false,
                'subscription_option'     => 'A',
                'subs_payment_date'       => null,
                'insurance_pcn'           => true,
                'no_email'                => true,
                'is_monthly'              => true,
                'is_posted'               => false,
                'is_cancelled'            => false,
                'log_book_sent'           => false,
                'contract_type'           => 'is_new_latest',
            ];
            $this->applyContractTypeSelection('is_new_latest');
        }

        if ($this->itemRows === []) {
            $this->addItemRow();
        }
    }

    public function updatedFormContractType(?string $value): void
    {
        $this->applyContractTypeSelection((string) ($value ?? ''));
    }

    public function updatedFormIsSubscription($value): void
    {
        $this->form['is_subscription'] = (bool) $value;

        if ($this->form['is_subscription'] && empty($this->form['subscription_option'])) {
            $this->form['subscription_option'] = 'A';
        }

        if (! $this->form['is_subscription']) {
            $this->form['subscription_option'] = null;
            if (! in_array(($this->form['contract_type'] ?? ''), ['is_new_latest', 'is_used_latest'], true)) {
                $this->form['subs_payment_date'] = null;
            }
        }
    }

    public function updatingCustomerSearch(): void
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerSuggestions = [];

            return;
        }

        $this->customerSuggestions = Customer::query()
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->customerSearch . '%')
                    ->orWhere('last_name', 'like', '%' . $this->customerSearch . '%')
                    ->orWhere('email', 'like', '%' . $this->customerSearch . '%')
                    ->orWhere('phone', 'like', '%' . $this->customerSearch . '%');
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone'])
            ->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->first_name . ' ' . $c->last_name,
                'sub'  => $c->email . ' · ' . $c->phone,
            ])
            ->toArray();
    }

    public function selectCustomer(int $id, string $name): void
    {
        $this->form['customer_id']    = $id;
        $this->customerSearch         = $name;
        $this->customerSuggestions    = [];
    }

    public function updatedMotorbikeSearches($value, $key): void
    {
        $index = (int) $key;

        if (strlen((string) $value) < 2) {
            $this->motorbikeSuggestions[$index] = [];

            return;
        }

        $this->motorbikeSuggestions[$index] = Motorbike::query()
            ->where(function ($q) use ($value) {
                $q->where('reg_no', 'like', '%' . $value . '%')
                    ->orWhere('make', 'like', '%' . $value . '%')
                    ->orWhere('model', 'like', '%' . $value . '%')
                    ->orWhere('vin_number', 'like', '%' . $value . '%');
            })
            ->limit(10)
            ->get(['id', 'reg_no', 'make', 'model', 'year', 'vin_number'])
            ->map(fn (Motorbike $motorbike) => [
                'id' => $motorbike->id,
                'label' => $motorbike->detail,
            ])
            ->toArray();
    }

    public function selectMotorbike(int $index, int $id, string $label): void
    {
        if (! isset($this->itemRows[$index])) {
            return;
        }

        $this->itemRows[$index]['motorbike_id'] = $id;
        $this->motorbikeSearches[$index] = $label;
        $this->motorbikeSuggestions[$index] = [];
    }

    public function addItemRow(): void
    {
        $this->itemRows[] = [
            'id' => null,
            'motorbike_id' => null,
            'is_posted' => true,
            'user_id' => backpack_user()?->id ?? auth()->id(),
        ];

        $index = array_key_last($this->itemRows);
        $this->motorbikeSearches[$index] = '';
        $this->motorbikeSuggestions[$index] = [];
    }

    public function removeItemRow(int $index): void
    {
        if (count($this->itemRows) <= 1) {
            $this->itemRows[$index] = [
                'id' => $this->itemRows[$index]['id'] ?? null,
                'motorbike_id' => null,
                'is_posted' => true,
                'user_id' => backpack_user()?->id ?? auth()->id(),
            ];
            $this->motorbikeSearches[$index] = '';
            $this->motorbikeSuggestions[$index] = [];

            return;
        }

        unset($this->itemRows[$index], $this->motorbikeSearches[$index], $this->motorbikeSuggestions[$index]);
        $this->itemRows = array_values($this->itemRows);
        $this->motorbikeSearches = array_values($this->motorbikeSearches);
        $this->motorbikeSuggestions = array_values($this->motorbikeSuggestions);
    }

    public function setContractType(string $type): void
    {
        $this->form['contract_type'] = $type;
        $this->applyContractTypeSelection($type);
    }

    protected function applyContractTypeSelection(string $type): void
    {
        $all = ['is_new', 'is_used', 'is_used_extended', 'is_used_extended_custom', 'is_new_latest', 'is_used_latest'];
        foreach ($all as $flag) {
            $this->form[$flag] = ($flag === $type);
        }

        if (! empty($this->form['is_subscription']) && empty($this->form['subscription_option'])) {
            $this->form['subscription_option'] = 'A';
        }

        if (empty($this->form['is_subscription']) && ! in_array($type, ['is_new_latest', 'is_used_latest'], true)) {
            $this->form['subs_payment_date'] = null;
        }

        if (! in_array($type, ['is_new', 'is_used'], true)) {
            $this->form['is_monthly'] = false;
        }
    }

    protected function resolveContractType(FinanceApplication $application): string
    {
        return match (true) {
            (bool) $application->is_new => 'is_new',
            (bool) $application->is_used_extended => 'is_used_extended',
            (bool) $application->is_new_latest => 'is_new_latest',
            (bool) $application->is_used_latest => 'is_used_latest',
            (bool) $application->is_used_extended_custom => 'is_used_extended_custom',
            (bool) $application->is_used => 'is_used',
            default => '',
        };
    }

    protected function formRules(): array
    {
        $subscriptionRequired = ! empty($this->form['is_subscription']);

        return [
            'form.customer_id'              => ['required', 'integer', 'exists:customers,id'],
            'form.user_id'                  => ['nullable', 'integer', 'exists:users,id'],
            'form.contract_type'            => ['required', Rule::in(['is_new', 'is_used', 'is_used_extended', 'is_used_extended_custom', 'is_new_latest', 'is_used_latest'])],
            'form.contract_date'            => ['required', 'date'],
            'form.first_instalment_date'    => ['nullable', 'date'],
            'form.motorbike_price'          => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'form.weekly_instalment'        => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'form.deposit'                  => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'form.extra'                    => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'form.extra_items'              => ['nullable', 'string'],
            'form.notes'                    => ['nullable', 'string'],
            'form.is_monthly'               => ['boolean'],
            'form.is_new'                   => ['boolean'],
            'form.is_used'                  => ['boolean'],
            'form.is_used_extended'         => ['boolean'],
            'form.is_new_latest'            => ['boolean'],
            'form.is_used_latest'           => ['boolean'],
            'form.is_used_extended_custom'  => ['boolean'],
            'form.is_subscription'          => ['boolean'],
            'form.subscription_option'      => [$subscriptionRequired ? 'required' : 'nullable', Rule::in(['A', 'B', 'C', 'D'])],
            'form.subs_payment_date'        => ['nullable', 'integer', 'min:1', 'max:31'],
            'form.insurance_pcn'            => ['boolean'],
            'form.no_email'                 => ['boolean'],
            'form.is_posted'                => ['boolean'],
            'form.is_cancelled'             => ['boolean'],
            'form.reason_of_cancellation'   => ['nullable', 'string'],
            'form.log_book_sent'            => ['boolean'],
            'form.logbook_transfer_date'    => ['nullable', 'date'],
            'itemRows'                      => ['required', 'array', 'min:1'],
            'itemRows.*.id'                 => ['nullable', 'integer', 'exists:application_items,id'],
            'itemRows.*.motorbike_id'       => ['required', 'integer', 'exists:motorbikes,id'],
            'itemRows.*.is_posted'          => ['boolean'],
            'itemRows.*.user_id'            => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules(), [
            'itemRows.min' => 'Add at least one application item.',
            'itemRows.*.motorbike_id.required' => 'Select a motorbike for each application item.',
        ]);
        $payload = $data['form'];
        $items = $data['itemRows'] ?? [];

        if (! collect($items)->contains(fn ($item) => ! empty($item['motorbike_id']))) {
            $this->addError('itemRows.0.motorbike_id', 'Select at least one motorbike for this finance application.');

            return;
        }

        $this->applyContractTypeSelection((string) ($payload['contract_type'] ?? ''));

        foreach (['is_used', 'is_used_extended', 'is_used_extended_custom', 'is_new_latest', 'is_used_latest'] as $flag) {
            $payload[$flag] = (bool) ($this->form[$flag] ?? false);
        }
        $payload['is_new'] = (bool) ($this->form['is_new'] ?? false);
        $payload['is_subscription'] = (bool) ($this->form['is_subscription'] ?? false);
        if (! $payload['is_subscription']) {
            $payload['subscription_option'] = null;
        }
        if (! $payload['is_subscription'] && ! $payload['is_new_latest'] && ! $payload['is_used_latest']) {
            $payload['subs_payment_date'] = null;
        }
        if (empty($payload['log_book_sent'])) {
            $payload['logbook_transfer_date'] = null;
        }

        $generationContext = [
            'insurance_pcn' => (bool) ($payload['insurance_pcn'] ?? true),
            'no_email' => (bool) ($payload['no_email'] ?? true),
            'is_new' => (bool) ($payload['is_new'] ?? false),
            'is_used' => (bool) ($payload['is_used'] ?? false),
            'is_used_extended' => (bool) ($payload['is_used_extended'] ?? false),
            'is_used_extended_custom' => (bool) ($payload['is_used_extended_custom'] ?? false),
            'is_new_latest' => (bool) ($payload['is_new_latest'] ?? false),
            'is_used_latest' => (bool) ($payload['is_used_latest'] ?? false),
            'is_subscription' => (bool) ($payload['is_subscription'] ?? false),
            'is_monthly' => (bool) ($payload['is_monthly'] ?? true),
            'log_book_sent' => (bool) ($payload['log_book_sent'] ?? false),
        ];

        unset($payload['insurance_pcn'], $payload['no_email']);
        unset($payload['contract_type']);

        if (empty($payload['user_id'])) {
            $payload['user_id'] = backpack_user()?->id ?? auth()->id();
        }

        if (empty($payload['user_id'])) {
            $this->addError('form.user_id', 'Unable to determine the staff user for this application.');

            return;
        }

        if (! empty($payload['is_cancelled']) && empty($payload['cancelled_at'])) {
            $payload['cancelled_at'] = now();
        } elseif (empty($payload['is_cancelled'])) {
            $payload['cancelled_at'] = null;
        }

        request()->attributes->set('skip_finance_agreement_generation', true);
        try {
            if ($this->application && $this->application->exists) {
                $this->application->update($payload);
                $this->application->refresh();
                $this->syncApplicationItems($items);
                $this->dispatch('flux-admin:toast', type: 'success', message: 'Finance application updated.');
            } else {
                $this->application = FinanceApplication::create($payload);
                $this->syncApplicationItems($items);
                $this->dispatch('flux-admin:toast', type: 'success', message: 'Finance application created.');
            }
        } finally {
            request()->attributes->set('skip_finance_agreement_generation', false);
        }

        request()->merge($generationContext);
        app(FinanceApplicationCrudController::class)->generateAgreementAccess($this->application);

        $this->redirect(route('flux-admin.finance.index'), navigate: true);
    }

    protected function syncApplicationItems(array $items): void
    {
        if (! $this->application || ! $this->application->exists) {
            return;
        }

        $keptIds = [];

        foreach ($items as $item) {
            if (empty($item['motorbike_id'])) {
                continue;
            }

            $payload = [
                'application_id' => $this->application->id,
                'motorbike_id' => (int) $item['motorbike_id'],
                'is_posted' => (bool) ($item['is_posted'] ?? true),
                'user_id' => $item['user_id'] ?? backpack_user()?->id ?? auth()->id(),
            ];

            if (! empty($item['id'])) {
                $applicationItem = ApplicationItem::where('application_id', $this->application->id)->find($item['id']);
                if ($applicationItem) {
                    $applicationItem->update($payload);
                    $keptIds[] = $applicationItem->id;
                }
            } else {
                $keptIds[] = ApplicationItem::create($payload)->id;
            }
        }

        ApplicationItem::where('application_id', $this->application->id)
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))
            ->delete();
    }

    public function render()
    {
        return view('flux-admin.pages.finance.form');
    }
}
