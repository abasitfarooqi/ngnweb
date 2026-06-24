<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\PaymentMethod;
use App\Models\RentingOtherCharge;
use App\Support\RentalBookingLifecycle;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class OtherChargesTab extends Component
{
    public int $bookingId;

    public string $description = '';
    public string $amount = '';
    public ?int $payingChargeId = null;
    public ?int $paymentMethodId = null;

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

        $this->description = '';
        $this->amount = '';
        $this->resetValidation();
        $this->flashMessage = 'Additional charge added.';
        $this->flashType    = 'success';
    }

    public function openPayModal(int $chargeId): void
    {
        $this->payingChargeId = $chargeId;
        $this->paymentMethodId = PaymentMethod::where('title', 'Cash')->value('id');
        $this->dispatch('open-modal', name: 'pay-charge-modal');
    }

    public function payCharge(): void
    {
        $this->validate([
            'payingChargeId'  => 'required|integer',
            'paymentMethodId' => 'required|integer|exists:payment_methods,id',
        ]);

        try {
            app(RentalBookingLifecycle::class)->payOtherCharge(
                $this->payingChargeId,
                $this->paymentMethodId
            );

            $this->payingChargeId = null;
            $this->dispatch('close-modal', name: 'pay-charge-modal');
            $this->flashMessage = 'Charge paid and transaction recorded.';
            $this->flashType    = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType    = 'error';
        }
    }

    public function render()
    {
        $charges = RentingOtherCharge::where('booking_id', $this->bookingId)
            ->orderByDesc('created_at')
            ->get();

        $totalAmount = $charges->sum(fn ($c) => (float) str_replace(',', '', $c->getRawOriginal('amount')));
        $paidAmount  = $charges->filter(fn ($c) => (bool) $c->getRawOriginal('is_paid'))
            ->sum(fn ($c) => (float) str_replace(',', '', $c->getRawOriginal('amount')));

        $paymentMethods = PaymentMethod::query()->where('is_enabled', true)->orderBy('title')->get();

        return view('flux-admin.partials.rentals.other-charges-tab', [
            'charges'        => $charges,
            'totalAmount'    => $totalAmount,
            'paidAmount'     => $paidAmount,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
