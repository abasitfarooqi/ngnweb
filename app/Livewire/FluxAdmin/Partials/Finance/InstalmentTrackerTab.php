<?php

namespace App\Livewire\FluxAdmin\Partials\Finance;

use App\Models\FinanceApplication;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class InstalmentTrackerTab extends Component
{
    public int $applicationId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $application = FinanceApplication::findOrFail($this->applicationId);
        $subscription = $application->judopaySubscription;

        return view('flux-admin.partials.finance.instalment-tracker', [
            'subscription' => $subscription,
        ]);
    }
}
