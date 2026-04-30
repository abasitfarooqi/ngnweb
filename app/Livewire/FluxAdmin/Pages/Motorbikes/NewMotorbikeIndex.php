<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NewMotorbike;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('New motorbikes — Flux Admin')]
class NewMotorbikeIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'new-motorbikes';
    }

    public function render()
    {
        $bikes = $this->baseQuery()
            ->with(['branch:id,name', 'user:id,first_name,last_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = \App\Models\Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.new-motorbikes-index', compact('bikes', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return NewMotorbike::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('VRM', 'like', "%{$term}%")
                        ->orWhere('make', 'like', "%{$term}%")
                        ->orWhere('model', 'like', "%{$term}%")
                        ->orWhere('VIM', 'like', "%{$term}%");
                });
            })
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('is_migrated') !== '', fn ($q) => $q->where('is_migrated', $this->filter('is_migrated') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with(['branch:id,name']);
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'VRM' => 'VRM',
            'Make' => 'make',
            'Model' => 'model',
            'Year' => 'year',
            'Colour' => 'colour',
            'Engine' => 'engine',
            'VIM' => 'VIM',
            'Branch' => fn ($b) => $b->branch?->name,
            'Status' => 'status',
            'Allocated' => fn ($b) => $b->is_migrated ? 'Yes' : 'No',
            'Purchase date' => fn ($b) => $b->purchase_date ? \Carbon\Carbon::parse($b->purchase_date)->format('Y-m-d') : '',
        ];
    }
}
