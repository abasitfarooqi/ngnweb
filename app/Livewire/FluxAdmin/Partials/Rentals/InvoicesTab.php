<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\RentingBooking;
use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use App\Services\RentingInvoiceSyncService;
use App\Support\FluxAdminAccess;
use App\Support\RentalBookingLifecycle;
use App\Support\RentalInvoiceTabData;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class InvoicesTab extends Component
{
    public int $bookingId;

    public bool $showPayModal = false;

    public ?int $payingInvoiceId = null;

    public ?int $paymentMethodId = null;

    public string $paymentKind = 'cash';

    public ?int $referralId = null;

    public ?int $directCustomerId = null;

    public string $referralSearch = '';

    public string $referralProof = '';

    public string $paymentAmount = '';

    public float $paymentOutstanding = 0.0;

    public ?int $expandedInvoiceId = null;

    /** @var array<string, mixed>|null */
    public ?array $expandedDetail = null;

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    #[On('rental-updated')]
    public function refreshInvoices(): void
    {
        $this->expandedDetail = $this->expandedInvoiceId
            ? RentalInvoiceTabData::detail($this->expandedInvoiceId)
            : null;
    }

    public function toggleInvoice(int $invoiceId): void
    {
        $invoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($invoiceId)
            ->first();

        if (! $invoice) {
            return;
        }

        if ($this->expandedInvoiceId === $invoiceId) {
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;

            return;
        }

        $this->expandedInvoiceId = $invoiceId;
        $this->expandedDetail = RentalInvoiceTabData::detail($invoiceId) ?? [];
    }

    public function openPayModal(int $invoiceId, float $outstandingBalance): void
    {
        $remaining = max($outstandingBalance, 0);

        $this->payingInvoiceId = $invoiceId;
        $this->paymentOutstanding = $remaining;
        $this->paymentKind = 'cash';
        $this->referralId = null;
        $this->directCustomerId = null;
        $this->referralSearch = '';
        $this->referralProof = '';
        $this->paymentMethodId = PaymentMethod::query()->where('slug', 'cash')->value('id')
            ?? PaymentMethod::query()->where('title', 'Cash')->value('id');
        $this->paymentAmount = number_format($remaining, 2, '.', '');
        $this->showPayModal = true;
    }

    public function closePayModal(): void
    {
        $this->showPayModal = false;
        $this->payingInvoiceId = null;
        $this->paymentKind = 'cash';
        $this->referralId = null;
        $this->directCustomerId = null;
        $this->referralSearch = '';
        $this->referralProof = '';
        $this->paymentAmount = '';
        $this->paymentOutstanding = 0.0;
    }

    public function updatedPaymentKind(string $kind): void
    {
        if (in_array($kind, ['referral', 'direct'], true)) {
            $this->paymentMethodId = null;
            $this->paymentAmount = number_format($this->paymentOutstanding, 2, '.', '');
            $this->referralId = $kind === 'referral' ? $this->referralId : null;
            $this->directCustomerId = $kind === 'direct' ? $this->directCustomerId : null;
            $this->referralSearch = '';
            $this->referralProof = '';

            return;
        }

        $this->referralId = null;
        $this->directCustomerId = null;
        $this->referralProof = '';
        $this->paymentMethodId = PaymentMethod::query()->where('slug', $kind)->value('id')
            ?? PaymentMethod::query()->where('title', ucfirst($kind))->value('id');
    }

    public function markPaid(): void
    {
        if ($this->paymentKind === 'referral') {
            $this->applyReferralPayment();

            return;
        }

        if ($this->paymentKind === 'direct') {
            $this->applyDirectFreeWeek();

            return;
        }

        $this->validate([
            'paymentMethodId' => 'required|integer|exists:payment_methods,id',
            'paymentAmount' => 'required|numeric|min:0.01',
            'payingInvoiceId' => 'required|integer',
        ]);

        try {
            $result = app(RentalBookingLifecycle::class)->recordPayment(
                $this->bookingId,
                $this->payingInvoiceId,
                $this->paymentMethodId,
                (float) $this->paymentAmount
            );

            $this->closePayModal();
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;

            $balance = (float) ($result['balance'] ?? 0);
            $this->flashMessage = $balance > 0
                ? 'Payment received. Remaining balance on this invoice: £'.number_format($balance, 2).'.'
                : 'Payment recorded. Invoice marked as paid.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    private function applyReferralPayment(): void
    {
        $this->validate([
            'referralId' => 'required|integer|exists:renting_referrals,id',
            'payingInvoiceId' => 'required|integer',
        ], [
            'referralId.required' => 'Select the referred customer for this programme free week.',
        ]);

        $booking = RentingBooking::query()->findOrFail($this->bookingId);
        $invoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($this->payingInvoiceId)
            ->firstOrFail();
        $referral = RentingReferral::query()->findOrFail((int) $this->referralId);

        if ((int) $referral->referrer_customer_id !== (int) $booking->customer_id) {
            $this->flashMessage = 'That referral does not belong to this rental customer.';
            $this->flashType = 'error';

            return;
        }

        $credit = $referral->credit();
        $needsEarlyApply = ! $credit?->isSpendable();
        if ($needsEarlyApply) {
            $this->validate([
                'referralProof' => 'required|string|min:8',
            ], [
                'referralProof.required' => 'Explain this early apply so the boss can check it.',
                'referralProof.min' => 'Explain this early apply so the boss can check it.',
            ]);
        }

        try {
            $staffId = FluxAdminAccess::user()?->getAuthIdentifier();
            $service = app(RentingReferralService::class);
            if ($needsEarlyApply) {
                $service->releaseEarly(
                    $referral,
                    $staffId ? (int) $staffId : null,
                    $this->referralProof,
                    $invoice
                );
            } else {
                $service->redeem(
                    $referral,
                    $invoice,
                    $staffId ? (int) $staffId : null
                );
            }
            $this->closePayModal();
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;
            $this->flashMessage = 'Referral free week applied. Invoice marked paid with a rental referral reward transaction.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    private function applyDirectFreeWeek(): void
    {
        $this->validate([
            'directCustomerId' => 'required|integer|exists:customers,id',
            'payingInvoiceId' => 'required|integer',
            'referralProof' => 'required|string|min:8',
        ], [
            'directCustomerId.required' => 'Search and select any customer this free week is linked to.',
            'referralProof.required' => 'Explain this free week so the boss can check it.',
            'referralProof.min' => 'Explain this free week so the boss can check it.',
        ]);

        $invoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($this->payingInvoiceId)
            ->firstOrFail();
        $selected = Customer::query()->findOrFail((int) $this->directCustomerId);

        try {
            $staffId = FluxAdminAccess::user()?->getAuthIdentifier();
            app(RentingReferralService::class)->applyDirectFreeWeek(
                $invoice,
                $selected,
                $staffId ? (int) $staffId : null,
                $this->referralProof
            );
            $this->closePayModal();
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;
            $this->flashMessage = 'Direct free week applied. Invoice marked paid. The boss has been emailed with your explanation.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function reversePayment(int $invoiceId): void
    {
        try {
            $invoice = BookingInvoice::where('booking_id', $this->bookingId)->findOrFail($invoiceId);
            app(RentalBookingLifecycle::class)->reversePayment($invoice);
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;
            $this->flashMessage = 'Latest payment reversed for invoice #'.$invoiceId.'. Customer notified.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function sendWhatsAppReminder(int $invoiceId): void
    {
        $detail = RentalInvoiceTabData::detail($invoiceId);
        if ($detail === null) {
            $this->flashMessage = 'Invoice not found.';
            $this->flashType = 'error';

            return;
        }

        BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($invoiceId)
            ->firstOrFail()
            ->update([
                'is_whatsapp_sent' => true,
                'whatsapp_last_reminder_sent_at' => now(),
            ]);

        if ($this->expandedInvoiceId === $invoiceId) {
            $this->expandedDetail = RentalInvoiceTabData::detail($invoiceId);
        }

        if (! empty($detail['whatsapp_url'])) {
            $this->js('window.open('.json_encode($detail['whatsapp_url']).', "_blank")');
        }

        $this->flashMessage = 'WhatsApp reminder marked as sent.'
            .(! empty($detail['whatsapp_url']) ? ' WhatsApp opened in a new tab.' : '');
        $this->flashType = 'success';
    }

    public function updateInvoiceDate(int $invoiceId, string $date): void
    {
        validator(['date' => $date], ['date' => ['required', 'date']])->validate();

        $invoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($invoiceId)
            ->firstOrFail();

        try {
            $result = app(RentingInvoiceSyncService::class)->resequenceUnpaidInvoiceDatesFrom($invoice->id, $date);
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';

            return;
        }

        if ($this->expandedInvoiceId === $invoiceId) {
            $this->expandedDetail = RentalInvoiceTabData::detail($invoiceId);
        }

        $this->flashMessage = ((int) $result['updated'] > 1)
            ? 'Invoice date updated. '.$result['updated'].' unpaid invoices were realigned to weekly dates.'
            : 'Invoice date updated.';
        $this->flashType = 'success';
        $this->dispatch('rental-updated');
    }

    public function render()
    {
        $invoices = RentalInvoiceTabData::rows($this->bookingId);
        $totalUnpaid = $invoices
            ->filter(fn ($invoice) => ! (bool) $invoice->is_paid && (bool) $invoice->is_due)
            ->sum('outstanding_balance');
        $booking = RentingBooking::query()->find($this->bookingId);
        $referralService = app(RentingReferralService::class);
        $programmeReferrals = ($booking?->customer_id)
            ? $referralService->approvedUnusedReferrals((int) $booking->customer_id)
            : collect();
        $spendableReferrals = $programmeReferrals
            ->filter(fn (RentingReferral $row) => $row->credit()?->isSpendable())
            ->values();
        $term = strtolower(trim($this->referralSearch));
        $matchedReferrals = $term === ''
            ? $programmeReferrals
            : $programmeReferrals->filter(function (RentingReferral $row) use ($term) {
                $haystack = strtolower(trim(implode(' ', [
                    $row->id,
                    $row->submitted_name,
                    $row->submitted_phone,
                    $row->referred?->first_name,
                    $row->referred?->last_name,
                    $row->referred?->phone,
                    $row->referred?->email,
                ])));

                return str_contains($haystack, $term);
            })->values();

        $directCustomers = collect();
        if ($this->paymentKind === 'direct' && strlen(trim($this->referralSearch)) >= 2) {
            $like = '%'.trim($this->referralSearch).'%';
            $directCustomers = Customer::query()
                ->where(function ($q) use ($like) {
                    $q->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                })
                ->orderBy('first_name')
                ->limit(12)
                ->get();
        }

        $selectedDirectCustomer = $this->directCustomerId
            ? Customer::query()->find($this->directCustomerId)
            : null;
        $selectedProgrammeReferral = $this->referralId
            ? $programmeReferrals->firstWhere('id', (int) $this->referralId)
            : null;

        $referrerEvidence = ['booking_id' => null, 'invoices' => [], 'missing' => false, 'message' => null];
        if ($this->paymentKind === 'direct' && $selectedDirectCustomer) {
            $referrerEvidence = $referralService->lastPaidInvoiceHistoryForCustomer((int) $selectedDirectCustomer->id);
        } elseif ($this->paymentKind === 'referral' && $booking?->customer_id) {
            $referrerEvidence = $referralService->lastPaidInvoiceHistoryForCustomer((int) $booking->customer_id);
        }

        return view('flux-admin.partials.rentals.invoices-tab', [
            'invoices' => $invoices,
            'totalUnpaid' => $totalUnpaid,
            'programmeReferrals' => $programmeReferrals,
            'spendableReferrals' => $spendableReferrals,
            'matchedReferrals' => $matchedReferrals,
            'directCustomers' => $directCustomers,
            'selectedDirectCustomer' => $selectedDirectCustomer,
            'referrerEvidence' => $referrerEvidence,
            'freeWeekAwards' => $referralService->awardsForBooking($this->bookingId),
            'expandedAward' => $this->expandedInvoiceId
                ? $referralService->awardSnapshotForInvoice((int) $this->expandedInvoiceId)
                : null,
            'needsEarlyApply' => $selectedProgrammeReferral
                ? ! $selectedProgrammeReferral->credit()?->isSpendable()
                : $spendableReferrals->isEmpty() && $programmeReferrals->isNotEmpty(),
        ]);
    }
}
