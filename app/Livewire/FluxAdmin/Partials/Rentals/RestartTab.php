<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingClosing;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Services\RentingInvoiceSyncService;
use App\Support\AdminDateTimeInput;
use App\Support\RentalBookingLifecycle;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class RestartTab extends Component
{
    public int $bookingId;

    public string $restartAt = '';

    public string $restartMode = 'reopen_ongoing';

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;
        $booking = RentingBooking::query()->findOrFail($bookingId);
        $this->restartAt = AdminDateTimeInput::toLocal($booking->start_date);
        $this->restartMode = app(RentalBookingLifecycle::class)->lifecycleStatus($booking) === RentalBookingLifecycle::STATUS_ENDED
            ? 'reopen_ongoing'
            : 'reset_draft';
    }

    public function executeRestart(RentingInvoiceSyncService $sync): void
    {
        $this->validate([
            'restartAt' => ['required', 'string'],
            'restartMode' => ['required', 'in:reopen_ongoing,reset_draft,resume_documents,resume_completed'],
        ]);

        $startAt = AdminDateTimeInput::parseStart($this->restartAt);
        $booking = RentingBooking::query()->findOrFail($this->bookingId);

        try {
            $result = app(RentalBookingLifecycle::class)->restartBooking($booking, $startAt, $this->restartMode);
            $tab = match ($this->restartMode) {
                'reset_draft', 'resume_documents' => 'documents',
                'resume_completed' => 'agreement',
                default => 'items',
            };
            $message = 'Booking #'.$result['booking_id'].' updated (same booking ID). '
                .$result['message'].' Start: '.$startAt->format('d M Y H:i').'. State: '.$result['state'].'.';

            if ($result['is_posted'] && in_array($this->restartMode, ['reopen_ongoing', 'resume_documents', 'resume_completed'], true)) {
                $syncResult = $sync->syncFutureInvoicesForBooking($this->bookingId);
                if (! $syncResult['skipped'] && ($syncResult['deleted'] > 0 || $syncResult['created'] > 0)) {
                    $message .= " Invoices: {$syncResult['deleted']} removed, {$syncResult['created']} created.";
                }
            }

            session()->flash('status', $message);

            $this->redirectRoute('flux-admin.rentals.show', [
                'booking' => $result['booking_id'],
                'tab'     => $tab,
            ], navigate: true);
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function render()
    {
        $booking = RentingBooking::query()
            ->with(['rentingBookingItems' => fn ($q) => $q->orderByDesc('id'), 'rentingBookingItems.motorbike'])
            ->findOrFail($this->bookingId);

        $lifecycle = app(RentalBookingLifecycle::class);
        $lifecycleStatus = $lifecycle->lifecycleStatus($booking);
        $closing = BookingClosing::query()->where('booking_id', $this->bookingId)->first();
        $latestItem = $booking->rentingBookingItems->first();
        $endedItem = $booking->rentingBookingItems->first(
            fn (RentingBookingItem $item) => $item->end_date !== null
        );

        return view('flux-admin.partials.rentals.restart-tab', [
            'booking'         => $booking,
            'lifecycleStatus' => $lifecycleStatus,
            'closing'         => $closing,
            'latestItem'      => $latestItem,
            'endedItem'       => $endedItem,
        ]);
    }
}
