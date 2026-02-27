<?php

namespace App\Livewire\Site;

use App\Models\Branch;
use Livewire\Component;

class Header extends Component
{
    public $selectedBranch;
    public $branches;
    public $mobileMenuOpen = false;

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->selectedBranch = session('selected_branch_id') ?? $this->branches->first()?->id ?? null;
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranch = $branchId;
        session(['selected_branch_id' => $branchId]);
        cookie()->queue('selected_branch_id', $branchId, 525600);
        $this->dispatch('branch-changed', branchId: $branchId);
    }

    public function toggleMobileMenu()
    {
        $this->mobileMenuOpen = !$this->mobileMenuOpen;
    }

    public function render()
    {
        return view('livewire.site.header');
    }
}
