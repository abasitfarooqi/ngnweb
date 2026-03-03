<?php

namespace App\Livewire\Site;

use App\Models\Branch;
use Livewire\Component;

class Header extends Component
{
    public $branches;
    public $selectedBranch;

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();

        if ($this->branches->isEmpty() && config('site.branches')) {
            $this->branches = collect(config('site.branches'))->map(fn ($b, $key) => (object) [
                'id' => $key,
                'name' => $b['name'] ?? ucfirst($key),
                'phone' => $b['phone'] ?? '',
            ]);
        }

        $this->selectedBranch = session('selected_branch_id') ?? $this->branches->first()?->id ?? null;
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranch = $branchId;
        session(['selected_branch_id' => $branchId]);
        cookie()->queue('selected_branch_id', $branchId, 525600);
        $this->dispatch('branch-changed', branchId: $branchId);
    }

    public function render()
    {
        return view('livewire.site.header');
    }
}
