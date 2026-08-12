<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use App\Models\RentingBookingItem;
use App\Services\RentingInvoiceSyncService;
use Livewire\Attributes\On;
use Livewire\Component;

class RentalPriceEditor extends Component
{
    public int $bookingId;

    public string $weeklyRent = '';

    public float $currentWeeklyRent = 0.0;

    public int $unpaidInvoiceCount = 0;

    public float $unpaidInvoiceTotal = 0.0;

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;
        $this->refreshPriceState();
    }

    #[On('rental-updated')]
    public function refreshPriceState(): void
    {
        $item = RentingBookingItem::query()
            ->where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->orderByDesc('id')
            ->first()
            ?: RentingBookingItem::query()
                ->where('booking_id', $this->bookingId)
                ->orderByDesc('id')
                ->first();

        $this->currentWeeklyRent = (float) ($item?->weekly_rent ?? 0);
        $this->weeklyRent = number_format($this->currentWeeklyRent, 2, '.', '');

        $unpaid = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->where('is_paid', false)
            ->where('amount', '>', 0);

        $this->unpaidInvoiceCount = (clone $unpaid)->count();
        $this->unpaidInvoiceTotal = (float) (clone $unpaid)->sum('amount');
    }

    public function saveWeeklyRent(): void
    {
        $this->validate([
            'weeklyRent' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ], [
            'weeklyRent.required' => 'Enter the weekly rental price.',
            'weeklyRent.numeric' => 'Weekly rental price must be a number.',
            'weeklyRent.min' => 'Weekly rental price cannot be negative.',
            'weeklyRent.max' => 'Weekly rental price is too high.',
        ]);

        try {
            $result = app(RentingInvoiceSyncService::class)->updateBookingWeeklyRent(
                $this->bookingId,
                round((float) $this->weeklyRent, 2)
            );

            $this->refreshPriceState();

            $completed = (int) ($result['invoices_completed'] ?? 0);
            $this->flashMessage = 'Weekly rent updated. '
                .$result['invoices_updated'].' unpaid invoice(s) updated'
                .($completed > 0 ? "; {$completed} covered by existing payments." : '.');
            $this->flashType = 'success';

            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function render()
    {
        return view('flux-admin.partials.rentals.rental-price-editor');
    }
}
