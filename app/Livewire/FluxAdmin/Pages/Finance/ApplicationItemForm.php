<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ApplicationItem;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Support\FluxAdminFormPayload;
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
            $this->form = array_intersect_key(
                $applicationItem->getAttributes(),
                array_flip(['application_id', 'motorbike_id', 'is_posted', 'app_id'])
            );
        } else {
            $this->form = ['is_posted' => false];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.application_id' => ['required', 'integer', 'exists:finance_applications,id'],
            'form.motorbike_id'     => ['required', 'integer', 'exists:motorbikes,id'],
            'form.app_id'           => ['nullable', 'integer'],
            'form.is_posted'        => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = FluxAdminFormPayload::onlyPersistable(ApplicationItem::class, $data['form']);
        $payload['is_posted'] = (bool) ($payload['is_posted'] ?? false);

        if (! ($payload['user_id'] ?? null)) {
            $payload['user_id'] = FluxAdminFormPayload::adminUserId();
        }

        if (! $payload['user_id']) {
            $this->addError('form.application_id', 'Could not determine staff user for this record.');

            return;
        }

        if ($this->applicationItem && $this->applicationItem->exists) {
            $this->applicationItem->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Application item updated.');
        } else {
            ApplicationItem::query()->create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Application item created.');
        }

        $this->redirect(route('flux-admin.application-items.index'), navigate: true);
    }

    public function render()
    {
        $applications = FinanceApplication::query()->latest('id')->limit(300)->get(['id', 'customer_id', 'status']);
        $motorbikes = Motorbike::query()->orderBy('reg_no')->limit(400)->get(['id', 'reg_no', 'make', 'model']);

        return view('flux-admin.pages.finance.application-item-form', compact('applications', 'motorbikes'));
    }
}
