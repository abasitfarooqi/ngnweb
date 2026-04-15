<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClubMember;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Members — Flux Admin')]
class ClubIndex extends Component
{
    use WithDataTable, WithPagination;

    public bool $activeOnly = true;

    public function mount(): void
    {
        $this->sortField = 'full_name';
        $this->sortDirection = 'asc';
    }

    public function updatingActiveOnly(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ClubMember::with('partner', 'customer');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('full_name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('vrm', 'like', "%{$this->search}%");
            });
        }

        if ($this->activeOnly) {
            $query->where('is_active', true);
        }

        $members = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.index', [
            'members' => $members,
        ]);
    }
}
