<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Customer;
use App\Models\FinanceApplication;
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

    public function mount(?FinanceApplication $application = null): void
    {
        $this->resetErrorBag();
        $this->application = $application;

        if ($application && $application->exists) {
            $application->load('customer');
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
            $this->form = $attrs;
            $this->customerSearch = $application->customer
                ? $application->customer->first_name . ' ' . $application->customer->last_name
                : '';
        } else {
            $nextFriday = now()->addDays(7 - (int) date('N') + 5);
            if ((int) date('N') >= 5) {
                $nextFriday = now()->addDays(12 - (int) date('N'));
            }

            $this->form = [
                'contract_date'           => now()->format('Y-m-d'),
                'first_instalment_date'   => $nextFriday->format('Y-m-d'),
                'is_used'                 => false,
                'is_used_extended_custom' => false,
                'is_new_latest'           => false,
                'is_used_latest'          => false,
                'is_subscription'         => false,
                'subscription_option'     => 'A',
                'is_monthly'              => false,
                'is_posted'               => false,
                'is_cancelled'            => false,
                'log_book_sent'           => false,
                'contract_type'           => '',
            ];
        }
    }

    public function updatedFormContractType(?string $value): void
    {
        $this->applyContractTypeSelection((string) ($value ?? ''));
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

    public function setContractType(string $type): void
    {
        $this->form['contract_type'] = $type;
        $this->applyContractTypeSelection($type);
    }

    protected function applyContractTypeSelection(string $type): void
    {
        $all = ['is_used', 'is_used_extended_custom', 'is_new_latest', 'is_used_latest', 'is_subscription'];
        foreach ($all as $flag) {
            $this->form[$flag] = ($flag === $type);
        }

        if ($type !== 'is_subscription') {
            $this->form['subscription_option'] = null;
        } elseif (empty($this->form['subscription_option'])) {
            $this->form['subscription_option'] = 'A';
        }

        if ($type !== 'is_used') {
            $this->form['is_monthly'] = false;
        }
    }

    protected function resolveContractType(FinanceApplication $application): string
    {
        return match (true) {
            (bool) $application->is_subscription => 'is_subscription',
            (bool) $application->is_new_latest => 'is_new_latest',
            (bool) $application->is_used_latest => 'is_used_latest',
            (bool) $application->is_used_extended_custom => 'is_used_extended_custom',
            (bool) $application->is_used => 'is_used',
            default => '',
        };
    }

    protected function formRules(): array
    {
        return [
            'form.customer_id'              => ['required', 'integer', 'exists:customers,id'],
            'form.contract_type'            => ['required', Rule::in(['is_used', 'is_used_extended_custom', 'is_new_latest', 'is_used_latest', 'is_subscription'])],
            'form.contract_date'            => ['required', 'date'],
            'form.first_instalment_date'    => ['nullable', 'date'],
            'form.motorbike_price'          => ['required', 'numeric', 'min:0'],
            'form.weekly_instalment'        => ['required', 'numeric', 'min:0'],
            'form.deposit'                  => ['required', 'numeric', 'min:0'],
            'form.extra'                    => ['nullable', 'numeric', 'min:0'],
            'form.extra_items'              => ['nullable', 'string'],
            'form.notes'                    => ['nullable', 'string'],
            'form.is_monthly'               => ['boolean'],
            'form.is_used'                  => ['boolean'],
            'form.is_new_latest'            => ['boolean'],
            'form.is_used_latest'           => ['boolean'],
            'form.is_used_extended'         => ['boolean'],
            'form.is_used_extended_custom'  => ['boolean'],
            'form.is_subscription'          => ['boolean'],
            'form.subscription_option'      => ['nullable', 'string', 'max:50'],
            'form.is_posted'                => ['boolean'],
            'form.is_cancelled'             => ['boolean'],
            'form.reason_of_cancellation'   => ['nullable', 'string'],
            'form.log_book_sent'            => ['boolean'],
            'form.logbook_transfer_date'    => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        $this->applyContractTypeSelection((string) ($payload['contract_type'] ?? ''));

        foreach (['is_used', 'is_used_extended_custom', 'is_new_latest', 'is_used_latest', 'is_subscription'] as $flag) {
            $payload[$flag] = (bool) ($this->form[$flag] ?? false);
        }
        if (! $payload['is_subscription']) {
            $payload['subscription_option'] = null;
        }
        if (empty($payload['log_book_sent'])) {
            $payload['logbook_transfer_date'] = null;
        }

        unset($payload['contract_type']);

        if (empty($payload['user_id'])) {
            $payload['user_id'] = backpack_user()?->id;
        }

        if ($this->application && $this->application->exists) {
            $this->application->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Finance application updated.');
        } else {
            FinanceApplication::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Finance application created.');
        }

        $this->redirect(route('flux-admin.finance.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.finance.form');
    }
}
