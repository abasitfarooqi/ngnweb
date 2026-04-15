<?php

namespace App\Livewire\FluxAdmin\Partials\Branches;

use App\Models\Branch;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class InfoTab extends Component
{
    public int $branchId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $branch = Branch::findOrFail($this->branchId);

        return view('flux-admin.partials.branches.info-tab', [
            'branch' => $branch,
        ]);
    }
}
