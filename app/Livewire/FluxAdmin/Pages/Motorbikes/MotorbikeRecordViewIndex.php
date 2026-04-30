<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeRecordView;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle history — Flux Admin')]
class MotorbikeRecordViewIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'vehicle-history';
        $this->sortField = 'START_DATE';
        $this->sortDirection = 'desc';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.record-view-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return MotorbikeRecordView::query()
            ->when($this->search, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('VRM', 'like', "%{$v}%")
                        ->orWhere('PERSON', 'like', "%{$v}%")
                        ->orWhere('DATABASE', 'like', "%{$v}%");
                });
            })
            ->when($this->filter('database'), fn ($q, $v) => $q->where('DATABASE', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'VRM' => 'VRM',
            'Database' => 'DATABASE',
            'Doc ID' => 'DOC_ID',
            'Person' => 'PERSON',
            'Start' => fn ($r) => $r->START_DATE,
            'End' => fn ($r) => $r->END_DATE,
        ];
    }
}
