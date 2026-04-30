<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\JudopayMitQueue;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Judopay MIT queue — Flux Admin')]
class MitQueueIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'judopay-mit-queue';
        $this->sortField = 'mit_fire_date';
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('flux-admin.pages.judopay.mit-queue-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return JudopayMitQueue::query()
            ->when($this->search, fn ($q, $v) => $q->where('judopay_payment_reference', 'like', "%{$v}%"))
            ->when($this->filter('cleared') !== '', fn ($q) => $q->where('cleared', $this->filter('cleared') === '1'))
            ->when($this->filter('fired') !== '', fn ($q) => $q->where('fired', $this->filter('fired') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'NGN MIT #' => 'ngn_mit_queue_id', 'Judo ref' => 'judopay_payment_reference',
            'Cleared' => fn ($r) => $r->cleared ? 'Yes' : 'No',
            'Cleared at' => fn ($r) => $r->cleared_at?->format('Y-m-d H:i'),
            'Fire date' => fn ($r) => $r->mit_fire_date ? \Carbon\Carbon::parse($r->mit_fire_date)->format('Y-m-d') : '',
            'Retry' => 'retry', 'Fired' => fn ($r) => $r->fired ? 'Yes' : 'No',
        ];
    }
}
