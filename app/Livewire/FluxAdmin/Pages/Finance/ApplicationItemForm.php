<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ApplicationItem;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ApplicationItemForm extends Component
{
    use WithAuthorization;

    public ?ApplicationItem $applicationItem = null;

    public array $form = [];

    public function mount(?ApplicationItem $applicationItem = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-finance-applications');
        $this->applicationItem = $applicationItem;

        if ($applicationItem && $applicationItem->exists) {
            $attrs = $applicationItem->getAttributes();
            foreach (['start_date', 'due_date', 'end_date'] as $field) {
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
            $this->form = ['is_posted' => false];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.application_id'    => ['required', 'integer'],
            'form.motorbike_id'      => ['required', 'integer'],
            'form.start_date'        => ['nullable', 'date'],
            'form.due_date'          => ['nullable', 'date'],
            'form.end_date'          => ['nullable', 'date'],
            'form.weekly_instalment' => ['nullable', 'numeric', 'min:0'],
            'form.is_posted'         => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];
        $payload['is_posted'] = (bool) ($payload['is_posted'] ?? false);

        if ($this->applicationItem && $this->applicationItem->exists) {
            $this->applicationItem->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Application item updated.');
        } else {
            ApplicationItem::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Application item created.');
        }

        $this->redirect(route('flux-admin.application-items.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.finance.application-item-form');
    }
}
