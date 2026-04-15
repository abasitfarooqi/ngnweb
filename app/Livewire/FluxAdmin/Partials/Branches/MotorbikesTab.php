<?php

namespace App\Livewire\FluxAdmin\Partials\Branches;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Motorbike;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
class MotorbikesTab extends Component
{
    use WithDataTable, WithPagination;

    public int $branchId;

    public function mount(): void
    {
        $this->sortField = 'reg_no';
        $this->sortDirection = 'asc';
    }

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $query = Motorbike::where('branch_id', $this->branchId);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reg_no', 'like', "%{$this->search}%")
                    ->orWhere('make', 'like', "%{$this->search}%")
                    ->orWhere('model', 'like', "%{$this->search}%");
            });
        }

        $motorbikes = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.partials.branches.motorbikes-tab', [
            'motorbikes' => $motorbikes,
        ]);
    }
}
