<?php

namespace App\Livewire\FluxAdmin\Partials\Pcn;

use App\Models\PcnCase;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class CaseDetailsTab extends Component
{
    public int $pcnCaseId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $pcnCase = PcnCase::with('customer', 'motorbike', 'user')->findOrFail($this->pcnCaseId);

        return view('flux-admin.partials.pcn.case-details-tab', [
            'pcnCase' => $pcnCase,
        ]);
    }
}
