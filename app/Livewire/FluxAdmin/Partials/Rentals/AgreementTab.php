<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\AgreementAccess;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AgreementTab extends Component
{
    public int $bookingId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $agreements = AgreementAccess::with('customer')
            ->where('booking_id', $this->bookingId)
            ->get();

        return view('flux-admin.partials.rentals.agreement-tab', [
            'agreements' => $agreements,
        ]);
    }
}
