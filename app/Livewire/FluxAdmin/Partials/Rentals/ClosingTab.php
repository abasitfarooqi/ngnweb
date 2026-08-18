<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Mail\RentalEndedWithPendingsMail;
use App\Mail\RentalPaymentReceipt;
use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Support\RentalBookingLifecycle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Lazy]
class ClosingTab extends Component
{
    use WithFileUploads;

    public int $bookingId;

    public bool $prefillCollect = false;

    public string $noticeDetails = '';

    public bool $noticeChecked = false;

    public string $collectDetails = '';

    public string $collectDate = '';

    public string $collectTime = '';

    public bool $collectChecked = false;

    public bool $proceedAnyway = false;

    public bool $damagesChecked = false;

    public bool $pcnChecked = false;

    public bool $pendingChecked = false;

    public bool $depositChecked = false;

    public string $depositRefundAmount = '';

    public string $depositReturnNotes = '';

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    public function mount(bool $prefillCollect = false): void
    {
        $this->prefillCollect = $prefillCollect;

        $closing = BookingClosing::where('booking_id', $this->bookingId)->first();

        if ($closing) {
            $this->noticeDetails = $closing->notice_details ?? '';
            $this->noticeChecked = (bool) $closing->notice_checked;
            $this->collectDetails = $closing->collect_details ?? '';
            $this->collectDate = $closing->collect_date
                ? \Carbon\Carbon::parse($closing->collect_date)->toDateString()
                : '';
            $this->collectTime = $closing->collect_time
                ? substr((string) $closing->collect_time, 0, 5)
                : '';
            $this->collectChecked = (bool) $closing->collect_checked;
            $this->damagesChecked = (bool) $closing->damages_checked;
            $this->pcnChecked = (bool) $closing->pcn_checked;
            $this->pendingChecked = (bool) $closing->pending_checked;
            $this->depositChecked = (bool) $closing->deposit_checked;
            $this->depositRefundAmount = $closing->deposit_refund_amount !== null
                ? number_format((float) $closing->deposit_refund_amount, 2, '.', '')
                : $this->defaultDepositRefundAmount();
            $this->depositReturnNotes = (string) ($closing->deposit_return_notes ?? '');
        } else {
            $this->depositRefundAmount = $this->defaultDepositRefundAmount();
        }

        if ($this->prefillCollect) {
            $this->prefillCollectMotorbike();
        }
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    #[On('prefill-collect-motorbike')]
    public function prefillCollectMotorbike(): void
    {
        $user = backpack_user() ?? auth()->user();
        $this->collectDate = now()->toDateString();
        $this->collectTime = now()->format('H:i');

        $name = trim((string) ($user->name ?? $user->full_name ?? (($user->first_name ?? '').' '.($user->last_name ?? ''))));
        $stamp = sprintf(
            'Ended by user #%s%s',
            $user?->id ?? '?',
            $name !== '' ? " ({$name})" : ''
        );

        if (trim($this->collectDetails) === '' || ! str_contains($this->collectDetails, 'Ended by user #')) {
            $this->collectDetails = trim($this->collectDetails) === ''
                ? $stamp
                : trim($this->collectDetails).' | '.$stamp;
        }

        $this->flashMessage = 'Collect date, time and ending staff filled. Confirm step 2 (Collect Motorbike) to end the rental.';
        $this->flashType = 'success';
    }

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
        $this->flashType = 'success';
    }

    public function saveCollectMotorbike(): void
    {
        try {
            $lifecycle = app(RentalBookingLifecycle::class);
            $booking = RentingBooking::with(['customer', 'rentingBookingItems'])->findOrFail($this->bookingId);

            if (! $this->collectDate) {
                $this->collectDate = now()->toDateString();
            }
            if (! $this->collectTime) {
                $this->collectTime = now()->format('H:i');
            }

            $pendings = $lifecycle->closingPendings($booking, $this->collectDate);

            if ($pendings['total'] > 0 && ! $this->proceedAnyway) {
                $this->flashMessage = 'Outstanding £'.number_format($pendings['total'], 2)
                    .' (rent £'.number_format($pendings['rental'], 2)
                    .', charges £'.number_format($pendings['additional'], 2)
                    .', PCN £'.number_format($pendings['pcn'], 2)
                    .'). Clear balances or tick proceed anyway — you will be responsible.';
                $this->flashType = 'error';

                return;
            }

            $this->validate(['collectChecked' => 'accepted'], [
                'collectChecked.accepted' => 'Please tick the checkbox to confirm motorbike collected.',
            ]);

            $item = RentingBookingItem::query()
                ->where('booking_id', $this->bookingId)
                ->whereNull('end_date')
                ->orderByDesc('id')
                ->first();

            if (! $item) {
                $this->flashMessage = 'This rental is already ended (no open booking item). Refresh the page.';
                $this->flashType = 'error';
                $this->dispatch('rental-updated');

                return;
            }

            $closing = $lifecycle->endRental(
                $booking,
                $item,
                [
                    'collect_details' => $this->collectDetails,
                    'collect_date' => $this->collectDate,
                    'collect_time' => $this->collectTime,
                    'collect_checked' => $this->collectChecked,
                ],
                $this->proceedAnyway && $pendings['total'] > 0
            );

            $this->collectDetails = (string) ($closing->collect_details ?? $this->collectDetails);
            $this->collectChecked = true;

            if ($this->proceedAnyway && $pendings['total'] > 0) {
                $this->notifyEndedWithPendings($booking, $pendings);
            }

            $this->flashMessage = 'Motorbike collection recorded — rental ended. Future invoices after '.$this->collectDate.' removed.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('saveCollectMotorbike failed: '.$e->getMessage(), [
                'booking_id' => $this->bookingId,
            ]);
            $this->flashMessage = 'Could not end rental: '.$e->getMessage();
            $this->flashType = 'error';
        }
    }

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
        $this->flashType = 'success';
    }

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
        $this->flashType = 'success';
    }

    public function savePendingRent(): void
    {
        $lifecycle = app(RentalBookingLifecycle::class);
        $booking = RentingBooking::with('rentingBookingItems')->findOrFail($this->bookingId);
        $pendings = $lifecycle->closingPendings($booking, $this->collectDate ?: null);

        if ($pendings['rental'] > 0) {
            $this->flashMessage = 'There is still £'.number_format($pendings['rental'], 2).' unpaid rent. Clear invoices on the Invoices tab first.';
            $this->flashType = 'error';

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
        $this->flashType = 'success';
    }

    public function saveDepositReturn(): void
    {
        $this->validate([
            'depositChecked' => ['accepted'],
            'depositRefundAmount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'depositReturnNotes' => ['required', 'string', 'max:2000'],
        ], [
            'depositChecked.accepted' => 'Please tick the checkbox to confirm deposit is returned.',
            'depositRefundAmount.required' => 'Enter the deposit refund amount.',
            'depositRefundAmount.numeric' => 'Deposit refund amount must be a valid number.',
            'depositReturnNotes.required' => 'Add deposit return notes or reason of deduction.',
        ]);

        $depositRefundAmount = round((float) $this->depositRefundAmount, 2);

        BookingClosing::updateOrCreate(
            ['booking_id' => $this->bookingId],
            [
                'deposit_checked' => $this->depositChecked,
                'deposit_refund_amount' => $depositRefundAmount,
                'deposit_return_notes' => trim($this->depositReturnNotes),
                'deposit_refunded_at' => now(),
                'deposit_refund_method' => 'Manual return',
                'deposit_refund_user_id' => auth()->id(),
                'deposit_refund_send_email' => true,
            ]
        );

        $this->depositRefundAmount = number_format($depositRefundAmount, 2, '.', '');

        try {
            $this->sendDepositReturnEmail($depositRefundAmount, trim($this->depositReturnNotes));
            $this->flashMessage = 'Deposit return details saved. Email sent to customer with Customer Service in CC.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            Log::error('Deposit return email failed: '.$e->getMessage(), [
                'booking_id' => $this->bookingId,
            ]);

            $this->flashMessage = 'Deposit return details saved, but email failed: '.$e->getMessage();
            $this->flashType = 'error';
        }
    }

    private function sendDepositReturnEmail(float $depositRefundAmount, string $notes): void
    {
        $booking = RentingBooking::with(['customer', 'rentingBookingItems.motorbike'])->findOrFail($this->bookingId);
        $customer = $booking->customer;

        if (! $customer?->email) {
            throw new \RuntimeException('Customer email is missing.');
        }

        $firstInvoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->first();

        $depositAmount = (float) ($firstInvoice?->deposit ?? 0);
        if ($depositAmount <= 0) {
            $depositAmount = (float) ($booking->deposit ?? 0);
        }

        $motorbike = $booking->rentingBookingItems->first()?->motorbike;
        $customerName = trim((string) (($customer->first_name ?? '').' '.($customer->last_name ?? '')));

        Mail::to($customer->email)
            ->cc('customerservice@neguinhomotors.co.uk')
            ->send(new RentalPaymentReceipt([
                'email' => [$customer->email],
                'title' => 'Rental Deposit Return',
                'subtitle' => 'Confirmation of rental deposit return.',
                'body' => 'Please find your deposit return details below.',
                'booking_id' => $booking->id,
                'invoice_id' => $firstInvoice?->id ?? 'N/A',
                'invoice_date' => $firstInvoice?->invoice_date,
                'transaction_id' => 'N/A',
                'transaction_date' => now(),
                'payment_method' => 'Deposit return',
                'amount' => $depositRefundAmount,
                'customer_name' => $customerName !== '' ? $customerName : 'Customer',
                'registration_number' => $motorbike?->reg_no,
                'invoice_amount' => $depositAmount,
                'invoice_amount_label' => 'Deposit Amount',
                'amount_label' => 'Amount Returned',
                'remaining_balance' => 0,
                'show_remaining_balance' => false,
                'invoice_status_label' => 'Deposit returned',
                'receipt_message' => 'Your rental deposit return has been recorded by NGN Motors.',
                'notes_label' => 'Reason of deduction / notes',
                'notes' => $notes,
            ]));
    }

    private function defaultDepositRefundAmount(): string
    {
        $firstInvoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->first();

        $depositAmount = (float) ($firstInvoice?->deposit ?? 0);
        if ($depositAmount <= 0) {
            $depositAmount = (float) (RentingBooking::query()->find($this->bookingId)?->deposit ?? 0);
        }

        return number_format($depositAmount, 2, '.', '');
    }

    public function render()
    {
        $lifecycle = app(RentalBookingLifecycle::class);
        $booking = RentingBooking::with(['rentingBookingItems'])->findOrFail($this->bookingId);
        $asOf = $this->collectDate !== '' ? $this->collectDate : now()->toDateString();
        $pendings = $lifecycle->closingPendings($booking, $asOf);

        $totalAdditional = (float) \App\Models\RentingOtherCharge::where('booking_id', $this->bookingId)->sum('amount');
        $paidAdditional = (float) \App\Models\RentingOtherCharge::where('booking_id', $this->bookingId)
            ->where('is_paid', true)
            ->sum('amount');

        return view('flux-admin.partials.rentals.closing-tab', [
            'booking' => $booking,
            'totalAdditional' => $totalAdditional,
            'paidAdditional' => $paidAdditional,
            'pcnTotal' => $pendings['pcn'],
            'pcnReceived' => 0.0,
            'pendingRent' => $pendings['rental'],
            'pendingAdditional' => $pendings['additional'],
            'pendingTotal' => $pendings['total'],
        ]);
    }

    /** @param  array{rental: float, additional: float, pcn: float, total: float}  $pendings */
    protected function notifyEndedWithPendings(RentingBooking $booking, array $pendings): void
    {
        $user = backpack_user() ?? auth()->user();
        $staffName = trim((string) ($user->name ?? $user->full_name ?? (($user->first_name ?? '').' '.($user->last_name ?? ''))));
        $recipients = config('mail.rental_ending_pending_notify', []);

        if ($user?->email) {
            $recipients[] = $user->email;
        }

        $recipients = array_values(array_unique(array_filter($recipients)));
        if ($recipients === []) {
            return;
        }

        $customer = $booking->customer;
        $customerName = $customer
            ? trim(($customer->first_name ?? '').' '.($customer->last_name ?? ''))
            : '—';

        $mailData = [
            'booking_id' => $booking->id,
            'customer_name' => $customerName,
            'staff_id' => $user?->id,
            'staff_name' => $staffName !== '' ? $staffName : 'Staff',
            'collect_date' => $this->collectDate,
            'collect_time' => $this->collectTime,
            'rental' => $pendings['rental'],
            'additional' => $pendings['additional'],
            'pcn' => $pendings['pcn'],
            'total' => $pendings['total'],
            'show_url' => null,
        ];

        try {
            $mailData['show_url'] = route('flux-admin.rentals.show', ['booking' => $booking->id]);
        } catch (\Throwable) {
            $mailData['show_url'] = url('/flux-admin/rentals/'.$booking->id);
        }

        try {
            Mail::to($recipients)->send(new RentalEndedWithPendingsMail($mailData));
        } catch (\Throwable $e) {
            Log::error('RentalEndedWithPendingsMail failed: '.$e->getMessage(), [
                'booking_id' => $booking->id,
            ]);
        }
    }
}
