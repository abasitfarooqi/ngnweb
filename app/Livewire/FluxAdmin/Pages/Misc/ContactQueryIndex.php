<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ContactQuery;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Contact queries — Flux Admin')]
class ContactQueryIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'contact-queries';
    }

    public function toggleDealt(int $id): void
    {
        $c = ContactQuery::findOrFail($id);
        $c->is_dealt = ! $c->is_dealt;
        $c->dealt_by_user_id = backpack_user()->id;
        $c->save();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Updated.');
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderByDesc('id')->paginate($this->perPage);

        return view('flux-admin.pages.misc.contact-queries-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return ContactQuery::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")->orWhere('subject', 'like', "%{$v}%")))
            ->when($this->filter('is_dealt') !== '', fn ($q) => $q->where('is_dealt', $this->filter('is_dealt') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return ['ID' => 'id', 'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'Subject' => 'subject', 'Message' => 'message', 'Dealt' => fn ($r) => $r->is_dealt ? 'Yes' : 'No', 'Received' => fn ($r) => $r->created_at?->format('Y-m-d H:i')];
    }
}
