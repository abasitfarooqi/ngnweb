<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ContactQuery;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ContactQueryForm extends Component
{
    use WithAuthorization;

    public ?ContactQuery $contactQuery = null;

    public array $form = [];

    public function mount(?ContactQuery $contactQuery = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->contactQuery = $contactQuery;

        if ($contactQuery && $contactQuery->exists) {
            $this->form = $contactQuery->getAttributes();
        } else {
            $this->form = ['is_dealt' => false];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.subject'  => ['nullable', 'string', 'max:255'],
            'form.notes'    => ['nullable', 'string'],
            'form.is_dealt' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->contactQuery && $this->contactQuery->exists) {
            $this->contactQuery->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Query updated.');
        } else {
            ContactQuery::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Query created.');
        }

        $this->redirect(route('flux-admin.contact-queries.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.misc.contact-query-form');
    }
}
