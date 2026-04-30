<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Support messages — Flux Admin')]
class SupportMessageIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

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
