<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NgnMitQueue;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('NGN MIT queue — Flux Admin')]
class NgnMitQueueIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'ngn-mit-queue';
        $this->sortField = 'mit_fire_date';
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('flux-admin.pages.judopay.ngn-mit-queue-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return NgnMitQueue::query()
            ->when($this->search, fn ($q, $v) => $q->where('invoice_number', 'like', "%{$v}%"))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('cleared') !== '', fn ($q) => $q->where('cleared', $this->filter('cleared') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Invoice' => 'invoice_number',
            'Invoice date' => fn ($r) => $r->invoice_date ? \Carbon\Carbon::parse($r->invoice_date)->format('Y-m-d') : '',
            'Fire date' => fn ($r) => $r->mit_fire_date ? \Carbon\Carbon::parse($r->mit_fire_date)->format('Y-m-d') : '',
            'Attempt' => 'mit_attempt', 'Status' => 'status',
            'Cleared' => fn ($r) => $r->cleared ? 'Yes' : 'No',
            'Cleared at' => fn ($r) => $r->cleared_at?->format('Y-m-d H:i'),
        ];
    }
}
