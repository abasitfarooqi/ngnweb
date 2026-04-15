<?php

namespace App\Livewire\FluxAdmin\Partials\Pcn;

use App\Models\PcnCase;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class PaymentsTab extends Component
{
    public int $pcnCaseId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $pcnCase = PcnCase::findOrFail($this->pcnCaseId);

        $payments = $pcnCase->payments()
            ->orderByDesc('payment_date')
            ->get();

        return view('flux-admin.partials.pcn.payments-tab', [
            'payments' => $payments,
        ]);
    }
}
