<?php

namespace App\Livewire\FluxAdmin\Pages\Branches;

use App\Models\Branch;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class BranchShow extends Component
{
    public Branch $branch;

    public string $activeTab = 'motorbikes';

    public function mount(Branch $branch): void
    {
        $this->branch = $branch;
    }

    public function getTitle(): string
    {
        return $this->branch->name . ' — Flux Admin';
    }

    public function render()
    {
        return view('flux-admin.pages.branches.show');
    }
}
