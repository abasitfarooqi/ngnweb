<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\JudopaySubscription;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Judopay subscription — Flux Admin')]
class SubscriptionForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');

        if ($id) {
            $this->recordId = $id;
            $record         = JudopaySubscription::findOrFail($id);
            $this->form     = $record->getAttributes();

            foreach (['start_date', 'end_date'] as $field) {
                if (! empty($this->form[$field])) {
                    try {
                        $this->form[$field] = \Carbon\Carbon::parse($this->form[$field])->format('Y-m-d');
                    } catch (\Throwable) {
                        $this->form[$field] = null;
                    }
                }
            }
        } else {
            $this->form = [
                'status'            => 'active',
                'billing_frequency' => 'weekly',
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.judopay_onboarding_id' => ['nullable', 'integer'],
            'form.billing_frequency'     => ['required', 'in:weekly,monthly,annually'],
            'form.billing_day'           => ['nullable', 'integer'],
            'form.amount'                => ['nullable', 'numeric'],
            'form.start_date'            => ['nullable', 'date'],
            'form.end_date'              => ['nullable', 'date'],
            'form.status'                => ['required', 'in:active,paused,completed,cancelled,inactive'],
            'form.consumer_reference'    => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'judopay_onboarding_id' => $this->form['judopay_onboarding_id'] ?? null,
            'billing_frequency'     => $this->form['billing_frequency'],
            'billing_day'           => $this->form['billing_day'] ?? null,
            'amount'                => $this->form['amount'] ?? null,
            'start_date'            => $this->form['start_date'] ?? null,
            'end_date'              => $this->form['end_date'] ?? null,
            'status'                => $this->form['status'],
            'consumer_reference'    => $this->form['consumer_reference'] ?? null,
        ];

        if ($this->recordId) {
            JudopaySubscription::findOrFail($this->recordId)->update($data);
        } else {
            JudopaySubscription::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Subscription saved.');
        $this->redirect(route('flux-admin.judopay-subscriptions.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.judopay.subscription-form');
    }
}
