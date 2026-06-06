<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\RentingOtherCharge;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class OtherChargesTab extends Component
{
    public int $bookingId;

    public string $description = '';
    public string $amount = '';

    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function addCharge(): void
    {
        $this->validate([
            'description' => 'required|string|min:3|max:255',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        RentingOtherCharge::create([
            'booking_id'  => $this->bookingId,
            'description' => $this->description,
            'amount'      => $this->amount,
            'is_paid'     => false,
        ]);

        $this->description  = '';
        $this->amount       = '';
        $this->resetValidation();

        $this->flashMessage = 'Additional charge added.';
        $this->flashType    = 'success';
    }

    public function markPaid(int $chargeId): void
    {
        $charge = RentingOtherCharge::where('booking_id', $this->bookingId)->findOrFail($chargeId);

        $charge->forceFill(['is_paid' => true])->save();

        $this->flashMessage = 'Charge marked as paid.';
        $this->flashType    = 'success';
    }

    public function render()
    {
        $charges = RentingOtherCharge::where('booking_id', $this->bookingId)
            ->orderByDesc('created_at')
            ->get();

        $totalAmount = $charges->sum(fn ($c) => (float) str_replace(',', '', $c->getRawOriginal('amount')));
        $paidAmount  = $charges->where('is_paid', 'Yes')->sum(fn ($c) => (float) str_replace(',', '', $c->getRawOriginal('amount')));

        return view('flux-admin.partials.rentals.other-charges-tab', [
            'charges'     => $charges,
            'totalAmount' => $totalAmount,
            'paidAmount'  => $paidAmount,
        ]);
    }
}
