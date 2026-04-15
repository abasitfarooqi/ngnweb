<?php

namespace App\Livewire\FluxAdmin\Partials\Finance;

use App\Models\FinanceApplication;
use App\Models\User;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ContractDetailsTab extends Component
{
    public int $applicationId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function getContractType(FinanceApplication $app): string
    {
        return match (true) {
            (bool) $app->is_subscription => 'Subscription',
            (bool) $app->is_new_latest => 'New Latest',
            (bool) $app->is_used_latest => 'Used Latest',
            (bool) $app->is_used_extended_custom => 'Used Extended Custom',
            (bool) $app->is_used_extended => 'Used Extended',
            (bool) $app->is_used => 'Used',
            default => 'Unknown',
        };
    }

    public function render()
    {
        $application = FinanceApplication::with('customer', 'user')->findOrFail($this->applicationId);
        $soldBy = $application->sold_by ? User::find($application->sold_by) : null;

        return view('flux-admin.partials.finance.contract-details', [
            'application' => $application,
            'soldBy' => $soldBy,
            'contractType' => $this->getContractType($application),
        ]);
    }
}
