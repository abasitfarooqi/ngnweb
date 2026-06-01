<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SupportConversation;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Support conversations — Flux Admin')]
class SupportConversationIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'last_message_at';
    }

    protected function formModel(): string
    {
        return SupportConversation::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.title' => ['nullable', 'string', 'max:255'],
            'formData.topic' => ['nullable', 'string', 'max:255'],
            'formData.status' => ['required', 'string'],
            'formData.customer_auth_id' => ['nullable', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['status' => 'open'];
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
        SupportConversation::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['customerAuth:id,email', 'assignedBackpackUser:id,first_name,last_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.support.conversations-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return SupportConversation::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('title', 'like', "%{$v}%")->orWhere('topic', 'like', "%{$v}%")->orWhere('uuid', 'like', "%{$v}%")))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('topic'), fn ($q, $v) => $q->where('topic', $v));
    }
}
