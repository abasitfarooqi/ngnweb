<?php

namespace App\Livewire\Components;

use App\Models\Branch;
use Livewire\Component;

class BranchSelector extends Component
{
    public $branches;
    public $selectedBranchId;
    public $selectedBranch;
    public $open = false;

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->selectedBranchId = session('selected_branch_id')
            ?? request()->cookie('selected_branch_id')
            ?? $this->branches->first()?->id ?? null;

        if ($this->selectedBranchId) {
            $this->selectedBranch = $this->branches->find($this->selectedBranchId);
        }
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->selectedBranch   = $this->branches->find($branchId);
        session(['selected_branch_id' => $branchId]);
        cookie()->queue('selected_branch_id', $branchId, 525600);
        $this->dispatch('branch-changed', branchId: $branchId);
        $this->open = false;
    }

    public function toggle()
    {
        $this->open = !$this->open;
    }

    public function render()
    {
        return view('livewire.components.branch-selector');
    }
}
