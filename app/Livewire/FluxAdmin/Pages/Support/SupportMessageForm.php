<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SupportMessage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class SupportMessageForm extends Component
{
    use WithAuthorization;

    public ?SupportMessage $supportMessage = null;

    public array $form = [];

    public function mount(?SupportMessage $supportMessage = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->supportMessage = $supportMessage;

        if ($supportMessage && $supportMessage->exists) {
            $this->form = $supportMessage->getAttributes();
        } else {
            $this->form = [
                'sender_type'   => 'staff',
                'sender_user_id' => backpack_user()->id,
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.conversation_id'           => ['required', 'integer'],
            'form.sender_type'               => ['required', 'in:customer,staff'],
            'form.body'                      => ['required', 'string'],
            'form.sender_user_id'            => ['nullable', 'integer'],
            'form.sender_customer_auth_id'   => ['nullable', 'integer'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->supportMessage && $this->supportMessage->exists) {
            $this->supportMessage->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Message updated.');
        } else {
            SupportMessage::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Message created.');
        }

        $this->redirect(route('flux-admin.support-messages.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.support.message-form');
    }
}
