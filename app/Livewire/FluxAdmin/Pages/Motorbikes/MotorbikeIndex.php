<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Branch;
use App\Models\Motorbike;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbikes — Flux Admin')]
class MotorbikeIndex extends Component
{
    use WithDataTable, WithPagination;

    public string $branch = '';

    public function mount(): void
    {
        $this->sortField = 'reg_no';
        $this->sortDirection = 'asc';
    }

    public function updatingBranch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Motorbike::with('vehicleProfile', 'branch')
            ->withCount('annualCompliances', 'repairs', 'rentingBookingItems');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reg_no', 'like', "%{$this->search}%")
                    ->orWhere('make', 'like', "%{$this->search}%")
                    ->orWhere('model', 'like', "%{$this->search}%")
                    ->orWhere('vin_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->branch !== '') {
            $query->where('branch_id', $this->branch);
        }

        $motorbikes = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.index', [
            'motorbikes' => $motorbikes,
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }
}
