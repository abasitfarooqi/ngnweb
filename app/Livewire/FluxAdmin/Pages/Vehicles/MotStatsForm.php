<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnMotNotifier;
use App\Support\FluxAdminFormPayload;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class MotStatsForm extends Component
{
    use WithAuthorization;

    public ?NgnMotNotifier $notifier = null;

    public array $form = [];

    public function mount(?NgnMotNotifier $notifier = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->notifier = $notifier;

        if ($notifier && $notifier->exists) {
            $attrs = $notifier->getAttributes();
            foreach (['mot_due_date', 'tax_due_date'] as $field) {
                if (! empty($attrs[$field])) {
                    try {
                        $attrs[$field] = Carbon::parse($attrs[$field])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$field] = null;
                    }
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = ['mot_notify_email' => false, 'mot_notify_phone' => false, 'customer_contact' => '', 'customer_email' => ''];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.customer_name'    => ['required', 'string', 'max:255'],
            'form.customer_contact' => ['nullable', 'string', 'max:50'],
            'form.customer_email'   => ['nullable', 'email', 'max:255'],
            'form.motorbike_reg'    => ['required', 'string', 'max:20'],
            'form.mot_due_date'     => ['nullable', 'date'],
            'form.tax_due_date'     => ['nullable', 'date'],
            'form.mot_notify_email' => ['nullable', 'boolean'],
            'form.mot_notify_phone' => ['nullable', 'boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = FluxAdminFormPayload::onlyPersistable(NgnMotNotifier::class, $data['form']);
        $payload['customer_contact'] = trim((string) ($payload['customer_contact'] ?? ''));
        $payload['customer_email'] = trim((string) ($payload['customer_email'] ?? ''));

        if ($this->notifier && $this->notifier->exists) {
            $this->notifier->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Notifier updated.');
        } else {
            NgnMotNotifier::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Notifier created.');
        }

        $this->redirect(route('flux-admin.mot-stats.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.mot-stats-form');
    }
}
