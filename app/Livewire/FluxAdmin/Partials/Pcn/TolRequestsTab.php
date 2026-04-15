<?php

namespace App\Livewire\FluxAdmin\Partials\Pcn;

use App\Models\PcnTolRequest;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class TolRequestsTab extends Component
{
    public int $pcnCaseId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $requests = PcnTolRequest::with('user')
            ->where('pcn_case_id', $this->pcnCaseId)
            ->orderByDesc('request_date')
            ->get();

        return view('flux-admin.partials.pcn.tol-requests-tab', [
            'requests' => $requests,
        ]);
    }
}
