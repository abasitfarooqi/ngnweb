<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\RentingBooking;
use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use App\Support\FluxAdminAccess;
use App\Support\RentingReferralAccess;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental referral — Flux Admin')]
class ReferralShow extends Component
{
    use WithAuthorization;

    public RentingReferral $referral;

    public string $reviewReason = '';

    public string $note = '';

    public string $matchSearch = '';

    public ?int $matchCustomerId = null;

    public ?int $redeemInvoiceId = null;

    public string $releaseReason = '';

    public function mount(RentingReferral $referral, RentingReferralService $service): void
    {
        $this->authorizeModule('see-menu-rentals');
        if ($referral->referrer) {
            $service->reconcileDirectAwardsAgainstProgramme($referral->referrer);
        }
        $this->referral = $service->refreshOpenReferral($referral)->load([
            'referrer',
            'referred',
            'ledger',
            'logs.changedBy',
            'referredQualifyingBooking',
            'referredQualifyingInvoice',
            'referrerQualifyingBooking',
            'reviewedBy',
        ]);
        $this->reviewReason = (string) ($this->referral->review_reason ?? '');
    }

    public function addNote(RentingReferralService $service): void
    {
        try {
            $service->addNote($this->referral, $this->note, $this->staffId());
        } catch (ValidationException $e) {
            throw $e;
        }
        $this->note = '';
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Note saved.');
    }

    public function approve(RentingReferralService $service): void
    {
        $this->guardReview();
        $override = RentingReferralAccess::isSuperAdmin();
        try {
            $service->approve($this->referral, $this->staffId(), $this->reviewReason, $override);
        } catch (\Throwable $e) {
            $this->addError('reviewReason', $e->getMessage());

            return;
        }
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Referral approved.');
    }

    public function reject(RentingReferralService $service): void
    {
        $this->guardReview();
        try {
            $service->reject($this->referral, $this->staffId(), $this->reviewReason);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->addError('reviewReason', $e->getMessage());

            return;
        }
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Referral rejected.');
    }

    public function undoReview(RentingReferralService $service): void
    {
        $this->guardReview();
        try {
            $service->undoReview($this->referral, $this->staffId());
        } catch (\Throwable $e) {
            $this->addError('reviewReason', $e->getMessage());

            return;
        }
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Review undone. This referral is waiting for approve or disapprove again.');
    }

    public function releaseEarly(RentingReferralService $service): void
    {
        $this->guardReview();
        $this->validate([
            'redeemInvoiceId' => 'required|integer|exists:booking_invoices,id',
            'releaseReason' => 'required|string|min:8',
        ], [
            'redeemInvoiceId.required' => 'Pick the unpaid invoice for this one-time free week.',
            'releaseReason.required' => 'Explain this early apply so the boss can check it.',
            'releaseReason.min' => 'Explain this early apply so the boss can check it.',
        ]);
        $invoice = BookingInvoice::query()->findOrFail((int) $this->redeemInvoiceId);
        try {
            $service->releaseEarly($this->referral, $this->staffId(), $this->releaseReason, $invoice);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->addError('releaseReason', $e->getMessage());

            return;
        }
        $this->releaseReason = '';
        $this->redeemInvoiceId = null;
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Free week applied. This reward is now locked.');
    }

    public function matchCustomer(RentingReferralService $service): void
    {
        if (! RentingReferralAccess::canView()) {
            abort(403);
        }

        $this->validate(['matchCustomerId' => 'required|integer|exists:customers,id']);
        $service->staffMatch($this->referral, (int) $this->matchCustomerId, RentingReferralAccess::isSuperAdmin());
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Customer matched.');
    }

    public function unmatch(RentingReferralService $service): void
    {
        $this->guardReview();
        $service->unmatch($this->referral, $this->reviewReason !== '' ? $this->reviewReason : 'Staff unmatch');
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Match removed.');
    }

    public function redeem(RentingReferralService $service): void
    {
        $this->guardReview();
        $this->validate([
            'redeemInvoiceId' => 'required|integer|exists:booking_invoices,id',
        ]);
        $invoice = BookingInvoice::query()->findOrFail((int) $this->redeemInvoiceId);
        if ($service->needsExtraFreeWeekProof((int) $this->referral->referrer_customer_id)) {
            $this->validate([
                'releaseReason' => 'required|string|min:8',
            ], [
                'releaseReason.required' => 'This person already has a free week. Explain why this extra free week is being given so the boss can check it.',
                'releaseReason.min' => 'This person already has a free week. Explain why this extra free week is being given so the boss can check it.',
            ]);
        }
        try {
            $service->redeem($this->referral, $invoice, $this->staffId(), $this->releaseReason);
        } catch (\Throwable $e) {
            $this->addError('redeemInvoiceId', $e->getMessage());

            return;
        }
        $this->releaseReason = '';
        $this->redeemInvoiceId = null;
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Free week applied. This reward is now locked.');
    }

    public function markAlreadyRedeemed(RentingReferralService $service): void
    {
        $this->guardReview();
        try {
            $service->markAlreadyRedeemedByDirect($this->referral, $this->staffId());
        } catch (\Throwable $e) {
            $this->addError('reviewReason', $e->getMessage());

            return;
        }
        $this->reload();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Points marked redeemed against the existing direct free week. No second week was applied.');
    }

    public function render(RentingReferralService $service)
    {
        $this->referral->load(['referrer', 'referred', 'ledger', 'logs.changedBy', 'referredQualifyingBooking', 'referredQualifyingInvoice']);

        $referrerHistory = $this->referral->referrer
            ? RentingBooking::query()->where('customer_id', $this->referral->referrer_customer_id)->orderByDesc('id')->limit(8)->get()
            : collect();
        $referredHistory = $this->referral->referred
            ? RentingBooking::query()->where('customer_id', $this->referral->referred_customer_id)->orderByDesc('id')->limit(8)->get()
            : collect();

        $matchChoices = collect();
        if (strlen(trim($this->matchSearch)) >= 2) {
            $term = '%'.trim($this->matchSearch).'%';
            $matchChoices = Customer::query()
                ->where(function ($q) use ($term) {
                    $q->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                })
                ->limit(12)
                ->get();
        }

        $portalLogs = $this->referral->logs->where('action', '!=', 'NOTE');
        $notes = $this->referral->logs->where('action', 'NOTE');

        $otherProgrammeReferrals = RentingReferral::query()
            ->where('referrer_customer_id', $this->referral->referrer_customer_id)
            ->where('id', '!=', $this->referral->id)
            ->with(['referred', 'ledger'])
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $referrerActiveBooking = ($this->referral->referrer && $this->referral->created_at)
            ? $service->activePostedBookingAt($this->referral->referrer, $this->referral->created_at)
            : null;

        return view('flux-admin.pages.rentals.referral-show', [
            'checks' => $service->investigationChecks($this->referral),
            'checkNote' => $service->investigationNote($this->referral),
            'referrerActiveBooking' => $referrerActiveBooking,
            'canReview' => RentingReferralAccess::canReview(),
            'isSuperAdmin' => RentingReferralAccess::isSuperAdmin(),
            'referrerHistory' => $referrerHistory,
            'referredHistory' => $referredHistory,
            'matchChoices' => $matchChoices,
            'redeemableInvoices' => $this->referral->referrer
                ? $service->redeemableInvoices($this->referral->referrer)
                : collect(),
            'credit' => $this->referral->credit(),
            'portalLogs' => $portalLogs,
            'notes' => $notes,
            'availablePoints' => $service->availablePoints((int) $this->referral->referrer_customer_id),
            'pendingPoints' => $service->pendingPoints((int) $this->referral->referrer_customer_id),
            'readyToApprove' => $service->readyToApprove($this->referral),
            'checkIsHealthy' => fn (string $key, bool $value) => $service->checkIsHealthy($key, $value),
            'programmeAwards' => $service->awardsForReferral((int) $this->referral->id),
            'directAwards' => $this->referral->referrer_customer_id
                ? $service->directAwardsForCustomer((int) $this->referral->referrer_customer_id)
                : collect(),
            'otherProgrammeReferrals' => $otherProgrammeReferrals,
            'needsExtraFreeWeekProof' => $this->referral->referrer_customer_id
                ? $service->needsExtraFreeWeekProof((int) $this->referral->referrer_customer_id)
                : false,
            'coveringDirectAward' => $service->coveringDirectAward($this->referral),
        ]);
    }

    private function guardReview(): void
    {
        if (! RentingReferralAccess::canReview()) {
            abort(403);
        }
    }

    private function staffId(): ?int
    {
        $user = FluxAdminAccess::user();

        return $user?->getAuthIdentifier() ? (int) $user->getAuthIdentifier() : null;
    }

    private function reload(): void
    {
        $this->referral = $this->referral->fresh([
            'referrer',
            'referred',
            'ledger',
            'logs.changedBy',
            'referredQualifyingBooking',
            'referredQualifyingInvoice',
            'reviewedBy',
        ]);
    }
}
