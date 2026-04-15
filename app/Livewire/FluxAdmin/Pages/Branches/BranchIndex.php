<?php

namespace App\Livewire\FluxAdmin\Pages\Branches;

use App\Models\Branch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Branches — Flux Admin')]
class BranchIndex extends Component
{
    public function render()
    {
        $branches = Branch::withCount('motorbikes')->orderBy('name')->get();

        return view('flux-admin.pages.branches.index', [
            'branches' => $branches,
        ]);
    }
}
