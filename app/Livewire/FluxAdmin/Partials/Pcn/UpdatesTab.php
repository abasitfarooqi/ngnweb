<?php

namespace App\Livewire\FluxAdmin\Partials\Pcn;

use App\Models\PcnCaseUpdate;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class UpdatesTab extends Component
{
    public int $pcnCaseId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $updates = PcnCaseUpdate::with('user')
            ->where('case_id', $this->pcnCaseId)
            ->orderByDesc('update_date')
            ->get();

        return view('flux-admin.partials.pcn.updates-tab', [
            'updates' => $updates,
        ]);
    }
}
