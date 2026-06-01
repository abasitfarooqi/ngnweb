<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SupportConversation;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class SupportConversationForm extends Component
{
    use WithAuthorization;

    public ?SupportConversation $supportConversation = null;

    public array $form = [];

    public function mount(?SupportConversation $supportConversation = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->supportConversation = $supportConversation;

        if ($supportConversation && $supportConversation->exists) {
            $this->form = $supportConversation->getAttributes();
        } else {
            $this->form = ['status' => 'open'];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.title'           => ['nullable', 'string', 'max:255'],
            'form.topic'           => ['nullable', 'string', 'max:255'],
            'form.status'          => ['required', 'string'],
            'form.customer_auth_id' => ['nullable', 'integer'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->supportConversation && $this->supportConversation->exists) {
            $this->supportConversation->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Conversation updated.');
        } else {
            SupportConversation::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Conversation created.');
        }

        $this->redirect(route('flux-admin.support-conversations.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.support.conversation-form');
    }
}
