<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeCatB;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Category B motorbikes — Flux Admin')]
class CatBIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'motorbikes-cat-b';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['motorbike:id,reg_no,make,model', 'branch:id,name'])
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.cat-b-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return MotorbikeCatB::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$term}%"));
            });
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['motorbike:id,reg_no', 'branch:id,name']); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Registration' => fn ($r) => $r->motorbike?->reg_no,
            'Date of purchase' => fn ($r) => $r->dop ? \Carbon\Carbon::parse($r->dop)->format('Y-m-d') : '',
            'Notes' => 'notes',
            'Branch' => fn ($r) => $r->branch?->name,
        ];
    }
}
