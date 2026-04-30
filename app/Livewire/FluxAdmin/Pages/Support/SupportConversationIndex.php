<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
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
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'last_message_at';
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
