<?php

namespace App\Livewire\FluxAdmin\Partials\Finance;

use App\Models\ContractAccess;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class SigningAccessTab extends Component
{
    public int $applicationId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $accesses = ContractAccess::with('customer')
            ->where('application_id', $this->applicationId)
            ->orderByDesc('id')
            ->get();

        return view('flux-admin.partials.finance.signing-access', [
            'accesses' => $accesses,
        ]);
    }
}
