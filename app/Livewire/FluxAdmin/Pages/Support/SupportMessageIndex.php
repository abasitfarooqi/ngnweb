<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SupportMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Support messages — Flux Admin')]
class SupportMessageIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    protected function formModel(): string
    {
        return SupportMessage::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.conversation_id' => ['required', 'integer'],
            'formData.sender_type' => ['required', 'in:customer,staff'],
            'formData.body' => ['required', 'string'],
            'formData.sender_user_id' => ['nullable', 'integer'],
            'formData.sender_customer_auth_id' => ['nullable', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'sender_type' => 'staff',
            'sender_user_id' => backpack_user()->id,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(SupportMessage::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        SupportMessage::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = SupportMessage::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('body', 'like', "%{$v}%")->orWhere('conversation_id', $v)))
            ->when($this->filter('conversation_id'), fn ($q, $v) => $q->where('conversation_id', $v))
            ->when($this->filter('sender_type'), fn ($q, $v) => $q->where('sender_type', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.support.support-messages-index', ['rows' => $rows]);
    }
}
