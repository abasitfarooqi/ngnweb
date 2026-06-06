<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingOtherCharge;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ClosingTab extends Component
{
    public int $bookingId;

    // Step 1 — Notice Period
    public string $noticeDetails = '';
    public bool $noticeChecked = false;

    // Step 2 — Collect Motorbike
    public string $collectDetails = '';
    public string $collectDate = '';
    public string $collectTime = '';
    public bool $collectChecked = false;

    // Step 3 — Damages: read-only totals, just checkbox
    public bool $damagesChecked = false;

    // Step 4 — PCN: read-only totals, just checkbox
    public bool $pcnChecked = false;

    // Step 5 — Pending Rent: read-only totals, just checkbox
    public bool $pendingChecked = false;

    // Step 6 — Deposit Return
    public bool $depositChecked = false;

    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function mount(): void
    {
        $closing = BookingClosing::where('booking_id', $this->bookingId)->first();

        if ($closing) {
            $this->noticeDetails  = $closing->notice_details ?? '';
            $this->noticeChecked  = (bool) $closing->notice_checked;
            $this->collectDetails = $closing->collect_details ?? '';
            $this->collectDate    = $closing->collect_date ?? '';
            $this->collectTime    = $closing->collect_time ?? '';
            $this->collectChecked = (bool) $closing->collect_checked;
            $this->damagesChecked = (bool) $closing->damages_checked;
            $this->pcnChecked     = (bool) $closing->pcn_checked;
            $this->pendingChecked = (bool) $closing->pending_checked;
            $this->depositChecked = (bool) $closing->deposit_checked;
        }
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    // Step 1
    public function saveNoticePeriod(): void
    {
        $this->validate(['noticeChecked' => 'accepted'], [
            'noticeChecked.accepted' => 'Please tick the checkbox to confirm notice period.',
        ]);

        BookingClosing::updateOrCreate(
            ['booking_id' => $this->bookingId],
            ['notice_details' => $this->noticeDetails, 'notice_checked' => $this->noticeChecked]
        );

        $this->flashMessage = 'Notice period saved.';
        $this->flashType    = 'success';
    }

    // Step 2
    public function saveCollectMotorbike(): void
    {
        $this->validate(['collectChecked' => 'accepted'], [
            'collectChecked.accepted' => 'Please tick the checkbox to confirm motorbike collected.',
        ]);

        BookingClosing::updateOrCreate(
            ['booking_id' => $this->bookingId],
            [
                'collect_details' => $this->collectDetails,
                'collect_date'    => $this->collectDate ?: null,
                'collect_time'    => $this->collectTime ?: null,
                'collect_checked' => $this->collectChecked,
            ]
        );

        // Set end_date on the active booking item
        $item = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->first();

        if ($item) {
            $item->update(['end_date' => $this->collectDate ?: now()->toDateString()]);
        }

        $this->flashMessage = 'Motorbike collection recorded.';
        $this->flashType    = 'success';
    }

    // Step 3
    public function saveDamagesCost(): void
    {
        $this->validate(['damagesChecked' => 'accepted'], [
            'damagesChecked.accepted' => 'Please tick the checkbox to confirm damages are cleared.',
        ]);

        BookingClosing::updateOrCreate(
            ['booking_id' => $this->bookingId],
            ['damages_checked' => $this->damagesChecked]
        );

        $this->flashMessage = 'Damages/additional cost step confirmed.';
        $this->flashType    = 'success';
    }

    // Step 4
    public function savePcnPendings(): void
    {
        $this->validate(['pcnChecked' => 'accepted'], [
            'pcnChecked.accepted' => 'Please tick the checkbox to confirm PCN pendings are cleared.',
        ]);

        BookingClosing::updateOrCreate(
            ['booking_id' => $this->bookingId],
            ['pcn_checked' => $this->pcnChecked]
        );

        $this->flashMessage = 'PCN pendings step confirmed.';
        $this->flashType    = 'success';
    }

    // Step 5
    public function savePendingRent(): void
    {
        $unpaidRent = BookingInvoice::where('booking_id', $this->bookingId)
            ->where('is_paid', false)
            ->where('invoice_date', '<=', now())
            ->sum('amount');

        if ($unpaidRent > 0) {
            $this->flashMessage = 'There is still £'.number_format($unpaidRent, 2).' unpaid rent. Clear all invoices in the Invoices tab first.';
            $this->flashType    = 'error';

            return;
        }

        $this->validate(['pendingChecked' => 'accepted'], [
            'pendingChecked.accepted' => 'Please tick the checkbox to confirm no pending rent.',
        ]);

        BookingClosing::updateOrCreate(
            ['booking_id' => $this->bookingId],
            ['pending_checked' => $this->pendingChecked]
        );

        $this->flashMessage = 'Pending rent step confirmed.';
        $this->flashType    = 'success';
    }

    // Step 6
    public function saveDepositReturn(): void
    {
        $this->validate(['depositChecked' => 'accepted'], [
            'depositChecked.accepted' => 'Please tick the checkbox to confirm deposit is returned.',
        ]);

        BookingClosing::updateOrCreate(
            ['booking_id' => $this->bookingId],
            ['deposit_checked' => $this->depositChecked]
        );

        $this->flashMessage = 'Deposit return step confirmed. Booking closing complete.';
        $this->flashType    = 'success';
    }

    public function render()
    {
        $booking = RentingBooking::findOrFail($this->bookingId);

        // Additional charges summary
        $totalAdditional  = RentingOtherCharge::where('booking_id', $this->bookingId)->sum('amount');
        $paidAdditional   = RentingOtherCharge::where('booking_id', $this->bookingId)->where('is_paid', true)->sum('amount');

        // PCN summary — outstanding (not closed) PCN for this booking's motorbike
        $pcnTotal    = 0;
        $pcnReceived = 0;
        $latestItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNotNull('motorbike_id')
            ->latest()
            ->first();

        if ($latestItem && $latestItem->motorbike_id) {
            $pcnTotal = \App\Models\PcnCase::where('motorbike_id', $latestItem->motorbike_id)
                ->where(fn ($q) => $q->where('isClosed', false)->orWhereNull('isClosed'))
                ->sum('full_amount') ?? 0;
        }

        // Pending rent
        $pendingRent = BookingInvoice::where('booking_id', $this->bookingId)
            ->where('is_paid', false)
            ->where('invoice_date', '<=', now())
            ->sum('amount');

        return view('flux-admin.partials.rentals.closing-tab', [
            'booking'          => $booking,
            'totalAdditional'  => (float) $totalAdditional,
            'paidAdditional'   => (float) $paidAdditional,
            'pcnTotal'         => (float) $pcnTotal,
            'pcnReceived'      => (float) $pcnReceived,
            'pendingRent'      => (float) $pendingRent,
        ]);
    }
}
