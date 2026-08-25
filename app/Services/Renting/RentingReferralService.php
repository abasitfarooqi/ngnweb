<?php

namespace App\Services\Renting;

use App\Mail\RentingDirectFreeWeekMail;
use App\Mail\RentingReferralApprovalReportMail;
use App\Mail\RentingReferralInvitationMail;
use App\Mail\RentingReferralRewardAvailableMail;
use App\Mail\RentingReferralStaffInvoiceMail;
use App\Mail\RentingReferralUnderReviewMail;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\RentingBooking;
use App\Models\RentingFreeWeekAward;
use App\Models\RentingReferral;
use App\Models\RentingReferralLog;
use App\Models\RentingReferralPointLedger;
use App\Models\RentingTransaction;
use App\Models\TransactionType;
use App\Support\FluxAdminAccess;
use App\Support\RentalBookingLifecycle;
use App\Support\RentalInvoiceTabData;
use App\Support\RentingReferralAccess;
use App\Support\RentingReferralIdentity;
use App\Support\RentingReferralSettings;
use App\Support\UkMobilePhone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class RentingReferralService
{
    public function referrerIsEligible(Customer $customer): bool
    {
        return $this->firstPaidWeeklyInvoiceForCustomer($customer) !== null;
    }

    public function create(
        Customer $referrer,
        array $submitted,
        string $source = RentingReferral::SOURCE_PORTAL,
        ?int $createdBy = null
    ): RentingReferral {
        $this->guardTables();

        if (! $this->referrerIsEligible($referrer)) {
            throw ValidationException::withMessages([
                'referrer' => 'You need at least one paid weekly rental invoice before you can refer a friend.',
            ]);
        }

        $name = trim((string) ($submitted['name'] ?? $submitted['submitted_name'] ?? ''));
        $phone = RentingReferralIdentity::phone((string) ($submitted['phone'] ?? $submitted['submitted_phone'] ?? ''));
        $email = RentingReferralIdentity::email($submitted['email'] ?? $submitted['submitted_email'] ?? null);

        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'Please enter their name.']);
        }

        if ($phone === null || ! UkMobilePhone::isValidMobile($phone)) {
            throw ValidationException::withMessages([
                'phone' => 'Please enter a valid UK mobile number starting with 07.',
            ]);
        }

        if ($this->identifiersMatchCustomer($referrer, $phone, $email)) {
            throw ValidationException::withMessages([
                'phone' => 'You cannot refer yourself.',
            ]);
        }

        $qualifyingBookingId = $this->firstPaidWeeklyInvoiceForCustomer($referrer)?->booking_id;

        $referral = DB::transaction(function () use ($referrer, $name, $phone, $email, $source, $createdBy, $qualifyingBookingId) {
            $row = RentingReferral::query()->create([
                'referral_code' => $this->uniqueCode(),
                'referrer_customer_id' => $referrer->id,
                'submitted_name' => $name,
                'submitted_phone' => $phone,
                'submitted_email' => $email,
                'status' => RentingReferral::STATUS_SUBMITTED,
                'source' => $source,
                'referrer_qualifying_booking_id' => $qualifyingBookingId,
                'created_by' => $createdBy,
                'warnings' => [],
            ]);

            $this->writeLog($row, 'CREATE', null, $row->only([
                'referral_code', 'referrer_customer_id', 'submitted_name', 'submitted_phone', 'submitted_email', 'status', 'source',
            ]));

            $this->applyMatch($row);

            return $row->fresh(['ledger']);
        });

        $this->sendInvitation($referral);

        return $referral;
    }

    public function matchOpenReferralsForCustomer(Customer $customer): void
    {
        if (! $this->tablesReady()) {
            return;
        }

        $phone = RentingReferralIdentity::phone($customer->phone)
            ?? RentingReferralIdentity::phone($customer->whatsapp);
        $email = RentingReferralIdentity::email($customer->email);

        $query = RentingReferral::query()
            ->whereNull('referred_customer_id')
            ->where('status', RentingReferral::STATUS_SUBMITTED);

        $query->where(function ($inner) use ($phone, $email) {
            if ($phone) {
                $inner->orWhere('submitted_phone', $phone);
            }
            if ($email) {
                $inner->orWhere('submitted_email', $email);
            }
        });

        if ($phone === null && $email === null) {
            return;
        }

        $query->orderBy('created_at')->orderBy('id')->get()->each(function (RentingReferral $referral) {
            try {
                $this->applyMatch($referral);
            } catch (Throwable $e) {
                Log::warning('renting_referral_match_failed', [
                    'referral_id' => $referral->id,
                    'message' => $e->getMessage(),
                ]);
            }
        });
    }

    public function syncCustomer(Customer $customer): void
    {
        if (! $this->tablesReady()) {
            return;
        }

        $this->matchOpenReferralsForCustomer($customer);

        RentingReferral::query()
            ->where('referred_customer_id', $customer->id)
            ->whereIn('status', [RentingReferral::STATUS_MATCHED, RentingReferral::STATUS_QUALIFYING])
            ->orderBy('id')
            ->get()
            ->each(function (RentingReferral $referral) {
                try {
                    $this->qualify($referral);
                } catch (Throwable $e) {
                    Log::warning('renting_referral_qualify_failed', [
                        'referral_id' => $referral->id,
                        'message' => $e->getMessage(),
                    ]);
                }
            });
    }

    public function syncPaidInvoice(BookingInvoice $invoice): void
    {
        if (! $this->tablesReady() || ! $invoice->is_paid) {
            return;
        }

        $booking = $invoice->booking ?: RentingBooking::query()->find($invoice->booking_id);
        if (! $booking?->customer_id) {
            return;
        }

        $customer = Customer::query()->find($booking->customer_id);
        if ($customer) {
            $this->syncCustomer($customer);
        }

        $this->reversePendingIfPaymentLost($invoice);
    }

    public function reversePendingIfPaymentLost(BookingInvoice $invoice): void
    {
        if (! $this->tablesReady() || $invoice->is_paid) {
            return;
        }

        $referrals = RentingReferral::query()
            ->where('referred_qualifying_invoice_id', $invoice->id)
            ->where('status', RentingReferral::STATUS_REVIEW)
            ->lockForUpdate()
            ->get();

        foreach ($referrals as $referral) {
            $credit = $this->creditFor($referral);
            if (! $credit || $credit->status !== RentingReferralPointLedger::STATUS_PENDING) {
                continue;
            }

            $old = $referral->only(['status', 'qualified_at', 'referred_qualifying_invoice_id']);
            $credit->update(['status' => RentingReferralPointLedger::STATUS_REVERSED]);
            $referral->update([
                'status' => RentingReferral::STATUS_QUALIFYING,
                'qualified_at' => null,
                'referred_qualifying_invoice_id' => null,
            ]);
            $this->writeLog($referral, 'REVERSE_PENDING', $old, $referral->fresh()->only(['status', 'qualified_at']));
        }
    }

    public function staffMatch(RentingReferral $referral, int $customerId, bool $superAdminException = false): RentingReferral
    {
        return DB::transaction(function () use ($referral, $customerId, $superAdminException) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();
            $customer = Customer::query()->findOrFail($customerId);
            $this->applyMatch($locked, $customer, $superAdminException);

            return $locked->fresh(['referrer', 'referred', 'ledger']);
        });
    }

    public function unmatch(RentingReferral $referral, string $reason): RentingReferral
    {
        return DB::transaction(function () use ($referral, $reason) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();
            $credit = $this->creditFor($locked);

            if ($credit && $credit->status === RentingReferralPointLedger::STATUS_REDEEMED) {
                throw new RuntimeException('This reward has already been used. Unmatch is not allowed.');
            }

            $old = $this->snapshot($locked);
            $payload = [
                'referred_customer_id' => null,
                'status' => RentingReferral::STATUS_SUBMITTED,
                'matched_at' => null,
                'qualified_at' => null,
                'referred_qualifying_booking_id' => null,
                'referred_qualifying_invoice_id' => null,
                'review_reason' => $reason,
            ];

            if ($credit && in_array($credit->status, [RentingReferralPointLedger::STATUS_PENDING, RentingReferralPointLedger::STATUS_AVAILABLE], true)) {
                $credit->update(['status' => RentingReferralPointLedger::STATUS_REVERSED]);
            }

            $locked->update($payload);
            $this->writeLog($locked, 'UNMATCH', $old, $locked->fresh()->toArray());

            return $locked->fresh();
        });
    }

    public function addNote(RentingReferral $referral, string $note, ?int $userId = null): void
    {
        $note = trim($note);
        if ($note === '') {
            throw ValidationException::withMessages(['note' => 'Please enter a note.']);
        }

        $this->writeLog($referral, 'NOTE', null, ['note' => $note], $userId);
    }

    public function approve(RentingReferral $referral, ?int $userId, string $reason = '', bool $superAdminOverride = false): RentingReferral
    {
        $this->assertCanReview($userId);

        return DB::transaction(function () use ($referral, $userId, $reason, $superAdminOverride) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();
            $credit = $this->creditFor($locked);

            if ($locked->status !== RentingReferral::STATUS_REVIEW && ! $superAdminOverride) {
                throw new RuntimeException('This referral is not waiting for approval.');
            }

            if (! $credit || $credit->status !== RentingReferralPointLedger::STATUS_PENDING) {
                if (! $superAdminOverride) {
                    throw new RuntimeException('Approve is only allowed after automatic qualification.');
                }
                $credit = $this->createPendingCredit($locked);
            }

            $waitDays = RentingReferralSettings::waitDays();
            $availableFrom = now()->addDays($waitDays);
            $old = $this->snapshot($locked);

            $credit->update([
                'status' => RentingReferralPointLedger::STATUS_AVAILABLE,
                'available_from' => $availableFrom,
                'original_available_from' => $availableFrom,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            $locked->update([
                'status' => RentingReferral::STATUS_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $userId,
                'review_reason' => $reason !== '' ? $reason : $locked->review_reason,
            ]);

            $this->writeLog($locked, 'APPROVE', $old, $this->snapshot($locked->fresh(['ledger'])));

            $fresh = $locked->fresh(['referrer', 'referred', 'ledger', 'logs', 'referredQualifyingBooking', 'referredQualifyingInvoice']);
            $this->sendApprovalReport($fresh, $userId);

            return $fresh;
        });
    }

    public function reject(RentingReferral $referral, ?int $userId, string $reason): RentingReferral
    {
        $this->assertCanReview($userId);
        $reason = trim($reason);
        if ($reason === '') {
            throw ValidationException::withMessages(['review_reason' => 'Please enter a reason.']);
        }

        return DB::transaction(function () use ($referral, $userId, $reason) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();
            $old = $this->snapshot($locked);
            $credit = $this->creditFor($locked);

            if ($credit && $credit->status === RentingReferralPointLedger::STATUS_REDEEMED) {
                throw new RuntimeException('This reward has already been used.');
            }

            if ($credit && in_array($credit->status, [RentingReferralPointLedger::STATUS_PENDING, RentingReferralPointLedger::STATUS_AVAILABLE], true)) {
                $credit->update(['status' => RentingReferralPointLedger::STATUS_REJECTED]);
            }

            $locked->update([
                'status' => RentingReferral::STATUS_REJECTED,
                'reviewed_at' => now(),
                'reviewed_by' => $userId,
                'review_reason' => $reason,
            ]);
            $this->writeLog($locked, 'REJECT', $old, $this->snapshot($locked->fresh()));

            return $locked->fresh();
        });
    }

    public function undoReview(RentingReferral $referral, ?int $userId): RentingReferral
    {
        $this->assertCanReview($userId);

        return DB::transaction(function () use ($referral, $userId) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();
            $credit = $this->creditFor($locked);

            if ($credit && $credit->status === RentingReferralPointLedger::STATUS_REDEEMED) {
                throw new RuntimeException('This free week has already been used. Undo is not allowed.');
            }

            if (! in_array($locked->status, [RentingReferral::STATUS_APPROVED, RentingReferral::STATUS_REJECTED], true)) {
                throw new RuntimeException('Undo is only for an approved or disapproved referral.');
            }

            $old = $this->snapshot($locked);

            if ($credit && in_array($credit->status, [
                RentingReferralPointLedger::STATUS_AVAILABLE,
                RentingReferralPointLedger::STATUS_REJECTED,
            ], true)) {
                $credit->update([
                    'status' => RentingReferralPointLedger::STATUS_PENDING,
                    'available_from' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                    'released_early_at' => null,
                    'released_early_by' => null,
                    'release_reason' => null,
                ]);
            }

            $locked->update([
                'status' => RentingReferral::STATUS_REVIEW,
                'reviewed_at' => now(),
                'reviewed_by' => $userId,
            ]);
            $this->writeLog($locked, 'UNDO_REVIEW', $old, $this->snapshot($locked->fresh(['ledger'])), $userId);

            return $locked->fresh(['ledger']);
        });
    }

    public function hold(RentingReferral $referral, ?int $userId, string $reason): RentingReferral
    {
        $this->assertCanReview($userId);
        $reason = trim($reason);
        if ($reason === '') {
            throw ValidationException::withMessages(['review_reason' => 'Please enter a reason.']);
        }

        $old = $this->snapshot($referral);
        $referral->update([
            'review_reason' => $reason,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);
        $this->writeLog($referral, 'HOLD', $old, ['review_reason' => $reason]);

        return $referral->fresh();
    }

    public function releaseEarly(RentingReferral $referral, ?int $userId, string $reason, BookingInvoice $invoice): RentingReferral
    {
        $this->assertCanReview($userId);

        if (! RentingReferralSettings::earlyReleaseAllowed() && ! RentingReferralAccess::isSuperAdmin()) {
            throw new RuntimeException('Early release is turned off.');
        }

        $reason = trim($reason);
        if ($reason === '') {
            throw ValidationException::withMessages(['release_reason' => 'Please enter a reason that Thiago can check.']);
        }

        $this->assertCanRedeem($userId);

        DB::transaction(function () use ($referral, $userId, $reason) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();
            $credit = $this->creditFor($locked);

            if ($locked->status !== RentingReferral::STATUS_APPROVED || ! $credit || $credit->status !== RentingReferralPointLedger::STATUS_AVAILABLE) {
                throw new RuntimeException('Early release is only available after approval.');
            }

            if ($credit->released_early_at || $credit->status === RentingReferralPointLedger::STATUS_REDEEMED) {
                throw new RuntimeException('Early release has already been used for this reward.');
            }

            $old = $credit->only(['available_from', 'released_early_at', 'release_reason']);
            $credit->update([
                'released_early_by' => $userId,
                'released_early_at' => now(),
                'release_reason' => $reason,
                'available_from' => now(),
            ]);
            $this->writeLog($locked, 'EARLY_RELEASE', $old, $credit->fresh()->only(['available_from', 'released_early_at', 'release_reason']), $userId);
        });

        return $this->redeem($referral->fresh(), $invoice, $userId, $reason);
    }

    public function redeem(RentingReferral $referral, BookingInvoice $invoice, ?int $userId, ?string $proof = null): RentingReferral
    {
        $this->assertCanRedeem($userId);
        $proof = trim((string) $proof);

        $result = DB::transaction(function () use ($referral, $invoice, $userId, $proof) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();
            $credit = $this->creditFor($locked);

            if ($credit && $credit->status === RentingReferralPointLedger::STATUS_REDEEMED) {
                throw new RuntimeException('This free week has already been used.');
            }

            if (! $credit || ! $credit->isSpendable()) {
                throw new RuntimeException('Points are not available to use yet.');
            }

            $invoice = BookingInvoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            $booking = RentingBooking::query()->whereKey($invoice->booking_id)->lockForUpdate()->firstOrFail();

            if ((int) $booking->customer_id !== (int) $locked->referrer_customer_id) {
                throw new RuntimeException('The reward can only be applied to the referrer’s invoices.');
            }

            if ($invoice->is_paid) {
                throw new RuntimeException('That invoice is already paid.');
            }

            if ((float) $invoice->amount <= 0) {
                throw new RuntimeException('That invoice has no rental amount.');
            }

            if (RentingReferralPointLedger::query()->where('redeemed_invoice_id', $invoice->id)->exists()) {
                throw new RuntimeException('A referral reward has already been applied to this invoice.');
            }

            $paid = (float) RentingTransaction::query()->where('invoice_id', $invoice->id)->sum('amount');
            $remaining = round((float) $invoice->amount - $paid, 2);
            if ($remaining <= 0) {
                throw new RuntimeException('That invoice has no remaining balance.');
            }

            $typeId = $this->rewardTransactionTypeId();
            $methodId = $this->rewardPaymentMethodId();

            $transaction = RentingTransaction::query()->create([
                'transaction_date' => now(),
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id,
                'transaction_type_id' => $typeId,
                'payment_method_id' => $methodId,
                'amount' => $remaining,
                'user_id' => $userId,
                'notes' => 'Rental referral #'.$locked->id.' — '.$credit->points.' points',
            ]);

            $invoice->update([
                'is_paid' => true,
                'is_posted' => true,
                'paid_date' => now(),
                'state' => 'Completed',
                'notes' => trim(((string) ($invoice->notes ?? ''))."\nRental referral #{$locked->id} — {$credit->points} points"),
            ]);

            $credit->update(['status' => RentingReferralPointLedger::STATUS_REDEEMED]);

            RentingReferralPointLedger::query()->create([
                'customer_id' => $locked->referrer_customer_id,
                'referral_id' => $locked->id,
                'direction' => RentingReferralPointLedger::DIRECTION_DEBIT,
                'status' => RentingReferralPointLedger::STATUS_REDEEMED,
                'points' => $credit->points,
                'redeemed_booking_id' => $booking->id,
                'redeemed_invoice_id' => $invoice->id,
                'redeemed_transaction_id' => $transaction->id,
            ]);

            $this->writeLog($locked, 'REDEEM', ['invoice_id' => null], [
                'invoice_id' => $invoice->id,
                'transaction_id' => $transaction->id,
                'amount' => $remaining,
                'proof' => $proof !== '' ? $proof : null,
            ], $userId);

            $hirer = Customer::query()->find($booking->customer_id);
            $referrer = $locked->referrer ?: Customer::query()->find($locked->referrer_customer_id);
            if ($hirer && $referrer) {
                $this->recordFreeWeekAward(
                    RentingFreeWeekAward::SOURCE_PROGRAMME,
                    $invoice,
                    $booking,
                    $transaction,
                    $remaining,
                    $hirer,
                    $referrer,
                    $userId,
                    $locked,
                    $proof !== '' ? $proof : null,
                );
            }

            return [
                'referral' => $locked->fresh(['referrer', 'referred', 'ledger', 'referrerQualifyingBooking', 'referredQualifyingBooking', 'referredQualifyingInvoice']),
                'invoice' => $invoice->fresh(),
                'booking' => $booking->fresh(),
                'transaction' => $transaction,
                'amount' => $remaining,
            ];
        });

        $this->sendStaffInvoiceNotice(
            $result['referral'],
            'redeemed',
            $userId,
            $result['invoice'],
            $result['booking'],
            $result['transaction'],
            $result['amount'],
            $proof !== '' ? $proof : null
        );

        $this->sendFreeWeekPaymentReceipt(
            $result['booking'],
            $result['invoice'],
            $result['transaction'],
            (float) $result['amount'],
            true
        );

        return $result['referral'];
    }

    public function applyDirectFreeWeek(
        BookingInvoice $invoice,
        Customer $selectedCustomer,
        ?int $userId,
        string $proof
    ): RentingTransaction {
        $this->assertCanRedeem($userId);
        $proof = trim($proof);
        if (strlen($proof) < 8) {
            throw ValidationException::withMessages([
                'referralProof' => 'Explain this free week so the boss can check it.',
            ]);
        }

        $result = DB::transaction(function () use ($invoice, $selectedCustomer, $userId, $proof) {
            $invoice = BookingInvoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            $booking = RentingBooking::query()->whereKey($invoice->booking_id)->lockForUpdate()->firstOrFail();

            if ($invoice->is_paid) {
                throw new RuntimeException('That invoice is already paid.');
            }

            if ((float) $invoice->amount <= 0) {
                throw new RuntimeException('That invoice has no rental amount.');
            }

            if ($this->invoiceHasReferralRedemption((int) $invoice->id)) {
                throw new RuntimeException('A free week has already been applied to this invoice.');
            }

            $paid = (float) RentingTransaction::query()->where('invoice_id', $invoice->id)->sum('amount');
            $remaining = round((float) $invoice->amount - $paid, 2);
            if ($remaining <= 0) {
                throw new RuntimeException('That invoice has no remaining balance.');
            }

            $transaction = RentingTransaction::query()->create([
                'transaction_date' => now(),
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id,
                'transaction_type_id' => $this->rewardTransactionTypeId(),
                'payment_method_id' => $this->rewardPaymentMethodId(),
                'amount' => $remaining,
                'user_id' => $userId,
                'notes' => 'Staff direct free week — selected customer #'.$selectedCustomer->id.' — '.$proof,
            ]);

            $invoice->update([
                'is_paid' => true,
                'is_posted' => true,
                'paid_date' => now(),
                'state' => 'Completed',
                'notes' => trim(((string) ($invoice->notes ?? ''))."\nStaff direct free week — customer #{$selectedCustomer->id}"),
            ]);

            $hirer = Customer::query()->find($booking->customer_id) ?: $selectedCustomer;
            $this->recordFreeWeekAward(
                RentingFreeWeekAward::SOURCE_DIRECT,
                $invoice,
                $booking,
                $transaction,
                $remaining,
                $hirer,
                $selectedCustomer,
                $userId,
                null,
                $proof,
            );

            return [
                'invoice' => $invoice->fresh(),
                'booking' => $booking->fresh(),
                'transaction' => $transaction,
                'amount' => $remaining,
            ];
        });

        $hirer = Customer::query()->find($result['booking']->customer_id);
        $to = RentingReferralSettings::approvalReportTo();
        $this->safeMail(fn () => Mail::to($to)->send(new RentingDirectFreeWeekMail(
            $result['booking'],
            $result['invoice'],
            $result['transaction'],
            $hirer ?: $selectedCustomer,
            $selectedCustomer,
            $proof,
            $userId,
            $result['amount']
        )));

        $this->sendFreeWeekPaymentReceipt(
            $result['booking'],
            $result['invoice'],
            $result['transaction'],
            (float) $result['amount'],
            false
        );

        return $result['transaction'];
    }

    /**
     * Last posted booking for this customer that has a paid weekly invoice, with payment-history fields.
     *
     * @return array{booking_id: int|null, invoices: list<array<string, mixed>>, missing: bool, message: string|null}
     */
    public function lastPaidInvoiceHistoryForCustomer(int $customerId): array
    {
        $empty = [
            'booking_id' => null,
            'invoices' => [],
            'missing' => true,
            'message' => RentingFreeWeekAward::ELIGIBILITY_FALLBACK,
        ];

        if ($customerId < 1) {
            return $empty;
        }

        $bookingIds = RentingBooking::query()
            ->where('customer_id', $customerId)
            ->where('is_posted', true)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->pluck('id');

        foreach ($bookingIds as $bookingId) {
            $paid = RentalInvoiceTabData::rows((int) $bookingId)
                ->filter(fn ($row) => (bool) $row->is_paid)
                ->values();

            if ($paid->isEmpty()) {
                continue;
            }

            return [
                'booking_id' => (int) $bookingId,
                'invoices' => $paid->map(fn ($row) => $this->snapshotPaidInvoiceRow($row, (int) $bookingId))->all(),
                'missing' => false,
                'message' => null,
            ];
        }

        return $empty;
    }

    /**
     * @param  object{
     *     id: mixed,
     *     transaction_no: mixed,
     *     invoice_date: mixed,
     *     amount: mixed,
     *     total_paid_amount: mixed,
     *     paid_date: mixed,
     *     state: mixed,
     *     deposit: mixed,
     *     received_by: mixed,
     *     transaction_datetime: mixed
     * }  $row
     * @return array<string, mixed>
     */
    private function snapshotPaidInvoiceRow(object $row, int $bookingId): array
    {
        return [
            'booking_id' => $bookingId,
            'invoice_id' => (int) $row->id,
            'transaction_no' => $row->transaction_no,
            'invoice_date' => $row->invoice_date,
            'invoice_amount' => (float) $row->amount,
            'paid_amount' => (float) $row->total_paid_amount,
            'paid_date' => $row->paid_date,
            'invoice_state' => $row->state,
            'deposit' => (float) $row->deposit,
            'received_by' => $row->received_by,
            'posting_time' => $row->transaction_datetime,
        ];
    }

    private function recordFreeWeekAward(
        string $source,
        BookingInvoice $invoice,
        RentingBooking $booking,
        RentingTransaction $transaction,
        float $amount,
        Customer $hirer,
        Customer $selectedReferrer,
        ?int $userId,
        ?RentingReferral $referral,
        ?string $proof,
    ): void {
        if (! Schema::hasTable('renting_free_week_awards')) {
            return;
        }

        $history = $this->lastPaidInvoiceHistoryForCustomer((int) $selectedReferrer->id);
        $proof = trim((string) $proof);

        RentingFreeWeekAward::query()->updateOrCreate(
            ['awarded_invoice_id' => $invoice->id],
            [
                'source' => $source,
                'referral_id' => $referral?->id,
                'awarded_booking_id' => $booking->id,
                'awarded_transaction_id' => $transaction->id,
                'amount' => $amount,
                'hirer_customer_id' => $hirer->id,
                'selected_referrer_customer_id' => $selectedReferrer->id,
                'selected_referrer_booking_id' => $history['booking_id'],
                'selected_paid_invoices' => $history['invoices'],
                'eligibility_note' => $history['missing'] ? RentingFreeWeekAward::ELIGIBILITY_FALLBACK : null,
                'staff_proof' => $proof !== '' ? $proof : null,
                'applied_by' => $userId,
            ]
        );
    }

    public function invoiceHasReferralRedemption(int $invoiceId): bool
    {
        if (! $this->tablesReady()) {
            return false;
        }

        if (RentingReferralPointLedger::query()
            ->where('redeemed_invoice_id', $invoiceId)
            ->where('direction', RentingReferralPointLedger::DIRECTION_DEBIT)
            ->exists()) {
            return true;
        }

        return RentingTransaction::query()
            ->where('invoice_id', $invoiceId)
            ->where('transaction_type_id', $this->rewardTransactionTypeId())
            ->exists();
    }

    public function awardsForBooking(int $bookingId): Collection
    {
        if (! Schema::hasTable('renting_free_week_awards')) {
            return collect();
        }

        return RentingFreeWeekAward::query()
            ->with(['selectedReferrer', 'hirer', 'referral', 'appliedBy'])
            ->where('awarded_booking_id', $bookingId)
            ->orderByDesc('id')
            ->get();
    }

    /** @return array<string, mixed>|null */
    public function awardSnapshotForInvoice(int $invoiceId): ?array
    {
        if (! Schema::hasTable('renting_free_week_awards')) {
            return null;
        }

        $award = RentingFreeWeekAward::query()
            ->with(['selectedReferrer', 'hirer', 'referral', 'appliedBy', 'selectedReferrerBooking'])
            ->where('awarded_invoice_id', $invoiceId)
            ->first();

        return $award?->toArray();
    }

    /** @return array<string, bool|string|null> */
    public function investigationChecks(RentingReferral $referral): array
    {
        $referrer = $referral->referrer;
        $referred = $referral->referred;
        $credit = $this->creditFor($referral);
        $earliestPosted = $referred
            ? RentingBooking::query()
                ->where('customer_id', $referred->id)
                ->where('is_posted', true)
                ->orderBy('start_date')
                ->orderBy('id')
                ->first()
            : null;

        $createdAfterStart = $this->referralCreatedAfterBookingStart($referral, $earliestPosted);

        $competing = $referred
            ? RentingReferral::query()
                ->where('referred_customer_id', $referred->id)
                ->where('id', '!=', $referral->id)
                ->whereIn('status', RentingReferral::ACTIVE_ATTRIBUTION_STATUSES)
                ->exists()
            : false;

        return [
            'referrer_qualified' => $referrer ? $this->referrerIsEligible($referrer) : false,
            'matched' => $referral->referred_customer_id !== null,
            'prior_rental' => $referred ? $this->hadPostedRentalBefore($referred, $referral->created_at ?? now()) : false,
            'paid_week' => $referral->referred_qualifying_invoice_id !== null
                || ($referred && $this->firstPaidWeeklyInvoiceForCustomer($referred, $referral->created_at) !== null),
            'duplicate' => $competing,
            'self_referral' => $referrer && $this->identifiersMatchCustomer($referrer, $referral->submitted_phone, $referral->submitted_email),
            'competing_referrer' => $competing,
            'reward_generated' => $credit !== null,
            'created_after_start' => $createdAfterStart,
        ];
    }

    public function checkIsHealthy(string $key, bool $value): bool
    {
        $noIsGood = ['prior_rental', 'duplicate', 'self_referral', 'competing_referrer', 'created_after_start'];

        return in_array($key, $noIsGood, true) ? ! $value : $value;
    }

    public function readyToApprove(RentingReferral $referral): bool
    {
        if ($referral->status !== RentingReferral::STATUS_REVIEW) {
            return false;
        }

        $credit = $this->creditFor($referral);
        if (! $credit || $credit->status !== RentingReferralPointLedger::STATUS_PENDING) {
            return false;
        }

        foreach ($this->investigationChecks($referral) as $key => $value) {
            if (is_bool($value) && ! $this->checkIsHealthy($key, $value)) {
                return false;
            }
        }

        return true;
    }

    public function notifySpendableRewards(): int
    {
        if (! $this->tablesReady()) {
            return 0;
        }

        $sent = 0;
        RentingReferral::query()
            ->where('status', RentingReferral::STATUS_APPROVED)
            ->with(['referrer', 'referred', 'ledger'])
            ->orderBy('id')
            ->get()
            ->each(function (RentingReferral $referral) use (&$sent) {
                $credit = $this->creditFor($referral);
                if (! $credit || ! $credit->isSpendable() || $credit->status === RentingReferralPointLedger::STATUS_REDEEMED) {
                    return;
                }

                $already = RentingReferralLog::query()
                    ->where('referral_id', $referral->id)
                    ->where('action', 'REWARD_READY')
                    ->exists();
                if ($already) {
                    return;
                }

                $this->sendRewardAvailable($referral);
                $this->sendStaffInvoiceNotice($referral, 'ready', $this->staffId());
                $this->writeLog($referral, 'REWARD_READY', null, [
                    'available_from' => optional($credit->available_from)?->toDateTimeString(),
                ]);
                $sent++;
            });

        return $sent;
    }

    /** @return Collection<int, RentingReferral> */
    public function approvedUnusedReferrals(int $referrerCustomerId): Collection
    {
        if (! $this->tablesReady()) {
            return collect();
        }

        return RentingReferral::query()
            ->where('referrer_customer_id', $referrerCustomerId)
            ->where('status', RentingReferral::STATUS_APPROVED)
            ->with(['referred', 'ledger'])
            ->orderByDesc('id')
            ->get()
            ->filter(function (RentingReferral $row) {
                $credit = $this->creditFor($row);

                return $credit && $credit->status === RentingReferralPointLedger::STATUS_AVAILABLE;
            })
            ->values();
    }

    /** @return Collection<int, RentingReferral> */
    public function spendableReferrals(int $referrerCustomerId): Collection
    {
        return $this->approvedUnusedReferrals($referrerCustomerId)
            ->filter(fn (RentingReferral $row) => $this->creditFor($row)?->isSpendable())
            ->values();
    }

    public function activePostedBookingAt(Customer $customer, Carbon $at): ?RentingBooking
    {
        return RentingBooking::query()
            ->where('customer_id', $customer->id)
            ->where('is_posted', true)
            ->where(function ($q) use ($at) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $at);
            })
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->first();
    }

    public function investigationNote(RentingReferral $referral): ?string
    {
        $referred = $referral->referred;
        if (! $referred || ! $referral->created_at) {
            return null;
        }

        $booking = RentingBooking::query()
            ->where('customer_id', $referred->id)
            ->where('is_posted', true)
            ->orderByDesc('id')
            ->first();

        if (! $booking) {
            return 'No posted rental for the matched customer yet. Refer first, then start their rental, then take one paid week.';
        }

        if ($this->referralCreatedAfterBookingStart($referral, $booking)) {
            return 'Paid invoices on booking #'.$booking->id.' do not count. That rental started '
                .$booking->start_date->format('d M Y')
                .', which is before this referral ('
                .$referral->created_at->format('d M Y H:i')
                .'). Refer first, then start the friend’s rental on or after that day.';
        }

        if ($referral->referred_qualifying_invoice_id) {
            return null;
        }

        $paid = $this->firstPaidWeeklyInvoiceForCustomer($referred, $referral->created_at);

        return $paid
            ? null
            : 'Matched, but there is not yet a paid weekly invoice on a rental that started on or after the referral.';
    }

    public function availablePoints(int $customerId): int
    {
        if (! $this->tablesReady()) {
            return 0;
        }

        return (int) RentingReferralPointLedger::query()
            ->where('customer_id', $customerId)
            ->where('direction', RentingReferralPointLedger::DIRECTION_CREDIT)
            ->where('status', RentingReferralPointLedger::STATUS_AVAILABLE)
            ->get()
            ->filter(fn (RentingReferralPointLedger $row) => $row->isSpendable())
            ->sum('points');
    }

    public function pendingPoints(int $customerId): int
    {
        if (! $this->tablesReady()) {
            return 0;
        }

        return (int) RentingReferralPointLedger::query()
            ->where('customer_id', $customerId)
            ->where('direction', RentingReferralPointLedger::DIRECTION_CREDIT)
            ->where('status', RentingReferralPointLedger::STATUS_PENDING)
            ->sum('points');
    }

    /** @return Collection<int, BookingInvoice> */
    public function redeemableInvoices(Customer $referrer): Collection
    {
        $bookingIds = RentingBooking::query()
            ->where('customer_id', $referrer->id)
            ->where('is_posted', true)
            ->pluck('id');

        if ($bookingIds->isEmpty()) {
            return collect();
        }

        $redeemedIds = RentingReferralPointLedger::query()
            ->whereNotNull('redeemed_invoice_id')
            ->pluck('redeemed_invoice_id');

        return BookingInvoice::query()
            ->whereIn('booking_id', $bookingIds)
            ->where('is_paid', false)
            ->where('amount', '>', 0)
            ->when($redeemedIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $redeemedIds))
            ->orderBy('invoice_date')
            ->get();
    }

    /** @return array<string, int|float> */
    public function bossMetrics(): array
    {
        if (! $this->tablesReady()) {
            return [
                'submitted' => 0,
                'review' => 0,
                'approved' => 0,
                'pending_points' => 0,
                'available_points' => 0,
                'redeemed_points' => 0,
                'redeemed_value' => 0.0,
                'warnings' => 0,
                'early_releases' => 0,
            ];
        }

        $statusCounts = RentingReferral::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $pending = (int) RentingReferralPointLedger::query()
            ->where('direction', 'credit')->where('status', 'pending')->sum('points');
        $available = (int) RentingReferralPointLedger::query()
            ->where('direction', 'credit')->where('status', 'available')->sum('points');
        $redeemed = (int) RentingReferralPointLedger::query()
            ->where('direction', 'debit')->where('status', 'redeemed')->sum('points');

        $redeemedValue = (float) RentingTransaction::query()
            ->whereIn('id', RentingReferralPointLedger::query()->whereNotNull('redeemed_transaction_id')->pluck('redeemed_transaction_id'))
            ->sum('amount');

        return [
            'submitted' => (int) ($statusCounts[RentingReferral::STATUS_SUBMITTED] ?? 0),
            'review' => (int) ($statusCounts[RentingReferral::STATUS_REVIEW] ?? 0),
            'approved' => (int) ($statusCounts[RentingReferral::STATUS_APPROVED] ?? 0),
            'pending_points' => $pending,
            'available_points' => $available,
            'redeemed_points' => $redeemed,
            'redeemed_value' => $redeemedValue,
            'warnings' => RentingReferral::query()->whereNotNull('warnings')->where('warnings', '!=', '[]')->count(),
            'early_releases' => RentingReferralPointLedger::query()->whereNotNull('released_early_at')->count(),
        ];
    }

    public function refreshOpenReferral(RentingReferral $referral): RentingReferral
    {
        $this->restoreStatusFromCredit($referral);
        $referral = $referral->fresh() ?? $referral;

        if (! $referral->referred_customer_id) {
            $this->applyMatch($referral);
            $referral = $referral->fresh() ?? $referral;
        }

        if (in_array($referral->status, [RentingReferral::STATUS_MATCHED, RentingReferral::STATUS_QUALIFYING], true)) {
            return $this->qualify($referral);
        }

        $this->attachQualifyingInvoiceIfMissing($referral);

        return $referral->fresh() ?? $referral;
    }

    public function qualify(RentingReferral $referral): RentingReferral
    {
        return DB::transaction(function () use ($referral) {
            $locked = RentingReferral::query()->whereKey($referral->id)->lockForUpdate()->firstOrFail();

            if (! in_array($locked->status, [RentingReferral::STATUS_MATCHED, RentingReferral::STATUS_QUALIFYING], true)) {
                return $locked;
            }

            if (! $locked->referred_customer_id) {
                $this->applyMatch($locked);
                $locked->refresh();
            }

            if (! $locked->referred_customer_id) {
                return $locked;
            }

            $referred = Customer::query()->find($locked->referred_customer_id);
            if (! $referred) {
                return $locked;
            }

            $invoice = $this->firstPaidWeeklyInvoiceForCustomer($referred, $locked->created_at);
            if (! $invoice) {
                if ($this->firstPostedBookingAfter($referred, $locked->created_at) && $locked->status === RentingReferral::STATUS_MATCHED) {
                    $locked->update(['status' => RentingReferral::STATUS_QUALIFYING]);
                    $this->writeLog($locked, 'QUALIFYING', ['status' => RentingReferral::STATUS_MATCHED], ['status' => RentingReferral::STATUS_QUALIFYING]);
                }

                return $locked->fresh();
            }

            $booking = RentingBooking::query()->find($invoice->booking_id);
            if ($this->referralCreatedAfterBookingStart($locked, $booking)) {
                $warnings = $locked->warnings ?? [];
                $warnings['created_after_start'] = true;
                $locked->update(['warnings' => $warnings]);

                return $locked->fresh();
            }

            if ($this->creditFor($locked)) {
                if (! $locked->referred_qualifying_invoice_id) {
                    $locked->update([
                        'referred_qualifying_booking_id' => $invoice->booking_id,
                        'referred_qualifying_invoice_id' => $invoice->id,
                        'qualified_at' => $locked->qualified_at ?? now(),
                    ]);
                }

                return $locked->fresh();
            }

            $old = $this->snapshot($locked);
            $locked->update([
                'status' => RentingReferral::STATUS_REVIEW,
                'qualified_at' => now(),
                'referred_qualifying_booking_id' => $invoice->booking_id,
                'referred_qualifying_invoice_id' => $invoice->id,
            ]);
            $this->createPendingCredit($locked);
            $this->writeLog($locked, 'QUALIFY', $old, $this->snapshot($locked->fresh(['ledger'])));

            $fresh = $locked->fresh(['referrer', 'referred', 'ledger', 'referredQualifyingBooking', 'referredQualifyingInvoice', 'referrerQualifyingBooking']);
            $this->sendUnderReview($fresh);

            return $fresh;
        });
    }

    private function applyMatch(RentingReferral $referral, ?Customer $forced = null, bool $superAdminException = false): void
    {
        if (in_array($referral->status, [
            RentingReferral::STATUS_REJECTED,
            RentingReferral::STATUS_CANCELLED,
            RentingReferral::STATUS_APPROVED,
            RentingReferral::STATUS_REVIEW,
        ], true) && $forced === null) {
            return;
        }

        if ($referral->referred_customer_id && $forced === null) {
            return;
        }

        $matches = $forced ? collect([$forced]) : $this->exactCustomerMatches($referral);
        $warnings = is_array($referral->warnings) ? $referral->warnings : [];

        if ($matches->count() > 1) {
            $warnings['multiple_matches'] = $matches->pluck('id')->all();
            $referral->update(['warnings' => $warnings]);
            $this->writeLog($referral, 'MATCH_WARNING', null, ['multiple_matches' => $warnings['multiple_matches']]);

            return;
        }

        $customer = $matches->first();
        if (! $customer) {
            $similar = $this->similarNameDobWarnings($referral);
            if ($similar !== []) {
                $warnings['similar_name_dob'] = $similar;
                $referral->update(['warnings' => $warnings]);
            }

            return;
        }

        if ((int) $customer->id === (int) $referral->referrer_customer_id
            || $this->identifiersMatchCustomer($referral->referrer ?: Customer::query()->find($referral->referrer_customer_id), $referral->submitted_phone, $referral->submitted_email)) {
            $referral->update([
                'status' => RentingReferral::STATUS_CANCELLED,
                'review_reason' => 'self_referral',
                'warnings' => array_merge($warnings, ['self_referral' => true]),
            ]);
            $this->writeLog($referral, 'CANCEL', null, ['reason' => 'self_referral']);

            return;
        }

        if ($this->hadPostedRentalBefore($customer, $referral->created_at ?? now())) {
            $referral->update([
                'status' => RentingReferral::STATUS_CANCELLED,
                'review_reason' => 'already_rented',
                'referred_customer_id' => $customer->id,
                'matched_at' => now(),
                'warnings' => array_merge($warnings, ['already_rented' => true]),
            ]);
            $this->writeLog($referral, 'CANCEL', null, ['reason' => 'already_rented', 'customer_id' => $customer->id]);

            return;
        }

        $winner = RentingReferral::query()
            ->where('referred_customer_id', $customer->id)
            ->whereIn('status', RentingReferral::ACTIVE_ATTRIBUTION_STATUSES)
            ->where('id', '!=', $referral->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->first();

        if ($winner) {
            $referral->update([
                'status' => RentingReferral::STATUS_CANCELLED,
                'review_reason' => 'already_attributed',
                'referred_customer_id' => $customer->id,
                'matched_at' => now(),
                'warnings' => array_merge($warnings, ['already_attributed' => $winner->id]),
            ]);
            $this->writeLog($referral, 'CANCEL', null, ['reason' => 'already_attributed', 'winner_id' => $winner->id]);

            return;
        }

        $postedAfter = $this->firstPostedBookingAfter($customer, $referral->created_at ?? now());
        if ($this->referralCreatedAfterBookingStart($referral, $postedAfter) && ! $superAdminException) {
            $warnings['created_after_start'] = true;
            $referral->update([
                'referred_customer_id' => $customer->id,
                'matched_at' => now(),
                'status' => RentingReferral::STATUS_CANCELLED,
                'review_reason' => 'created_after_start',
                'warnings' => $warnings,
            ]);
            $this->writeLog($referral, 'CANCEL', null, ['reason' => 'created_after_start']);

            return;
        }

        $status = $postedAfter ? RentingReferral::STATUS_QUALIFYING : RentingReferral::STATUS_MATCHED;
        $old = $this->snapshot($referral);
        $referral->update([
            'referred_customer_id' => $customer->id,
            'matched_at' => $referral->matched_at ?? now(),
            'status' => $status,
            'warnings' => $warnings,
        ]);
        $this->writeLog($referral, 'MATCH', $old, ['referred_customer_id' => $customer->id, 'status' => $status]);

        if ($status === RentingReferral::STATUS_QUALIFYING) {
            $this->qualify($referral->fresh());
        }
    }

    /** @return Collection<int, Customer> */
    private function exactCustomerMatches(RentingReferral $referral): Collection
    {
        $phone = RentingReferralIdentity::phone($referral->submitted_phone);
        $email = RentingReferralIdentity::email($referral->submitted_email);

        $query = Customer::query();
        $query->where(function ($inner) use ($phone, $email) {
            if ($phone) {
                $inner->orWhere('phone', $phone)
                    ->orWhere('whatsapp', $phone)
                    ->orWhere('emergency_contact', $phone);
            }
            if ($email) {
                $inner->orWhereRaw('LOWER(TRIM(email)) = ?', [$email]);
            }
        });

        if ($phone === null && $email === null) {
            return collect();
        }

        return $query->get()->filter(function (Customer $customer) use ($phone, $email) {
            $phones = array_filter([
                RentingReferralIdentity::phone($customer->phone),
                RentingReferralIdentity::phone($customer->whatsapp),
                RentingReferralIdentity::looksLikeMobile($customer->emergency_contact)
                    ? RentingReferralIdentity::phone($customer->emergency_contact)
                    : null,
            ]);

            if ($phone && in_array($phone, $phones, true)) {
                return true;
            }

            return $email !== null && RentingReferralIdentity::email($customer->email) === $email;
        })->values();
    }

    /** @return list<int> */
    private function similarNameDobWarnings(RentingReferral $referral): array
    {
        $name = RentingReferralIdentity::compactName($referral->submitted_name);
        if ($name === '') {
            return [];
        }

        return Customer::query()
            ->select(['id', 'first_name', 'last_name', 'dob'])
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->filter(function (Customer $customer) use ($name) {
                $full = trim($customer->first_name.' '.$customer->last_name);

                return RentingReferralIdentity::namesLookSimilar($name, $full);
            })
            ->take(5)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function identifiersMatchCustomer(?Customer $customer, ?string $phone, ?string $email): bool
    {
        if (! $customer) {
            return false;
        }

        $submittedPhone = RentingReferralIdentity::phone($phone);
        $submittedEmail = RentingReferralIdentity::email($email);

        $customerPhones = array_filter([
            RentingReferralIdentity::phone($customer->phone),
            RentingReferralIdentity::phone($customer->whatsapp),
            RentingReferralIdentity::looksLikeMobile($customer->emergency_contact)
                ? RentingReferralIdentity::phone($customer->emergency_contact)
                : null,
        ]);

        if ($submittedPhone && in_array($submittedPhone, $customerPhones, true)) {
            return true;
        }

        return $submittedEmail !== null && RentingReferralIdentity::email($customer->email) === $submittedEmail;
    }

    private function hadPostedRentalBefore(Customer $customer, Carbon $before): bool
    {
        return RentingBooking::query()
            ->where('customer_id', $customer->id)
            ->where('is_posted', true)
            ->where(function ($q) use ($before) {
                $q->where('start_date', '<', $before->copy()->startOfDay())
                    ->orWhere(function ($inner) use ($before) {
                        $inner->whereNull('start_date')->where('created_at', '<', $before);
                    });
            })
            ->exists();
    }

    private function firstPostedBookingAfter(Customer $customer, Carbon $after): ?RentingBooking
    {
        return RentingBooking::query()
            ->where('customer_id', $customer->id)
            ->where('is_posted', true)
            ->where(function ($q) use ($after) {
                $q->where('start_date', '>=', $after->copy()->startOfDay())
                    ->orWhere(function ($inner) use ($after) {
                        $inner->whereNull('start_date')->where('created_at', '>=', $after);
                    });
            })
            ->orderBy('start_date')
            ->orderBy('id')
            ->first();
    }

    public function firstPaidWeeklyInvoiceForCustomer(Customer $customer, ?Carbon $after = null): ?BookingInvoice
    {
        $bookingIds = RentingBooking::query()
            ->where('customer_id', $customer->id)
            ->where('is_posted', true)
            ->when($after, function ($q) use ($after) {
                $q->where(function ($inner) use ($after) {
                    $inner->where('start_date', '>=', $after->copy()->startOfDay())
                        ->orWhere(function ($nested) use ($after) {
                            $nested->whereNull('start_date')->where('created_at', '>=', $after);
                        });
                });
            })
            ->pluck('id');

        if ($bookingIds->isEmpty()) {
            return null;
        }

        return BookingInvoice::query()
            ->whereIn('booking_id', $bookingIds)
            ->where('is_paid', true)
            ->where('amount', '>', 0)
            ->whereHas('transactions')
            ->orderBy('paid_date')
            ->orderBy('id')
            ->first()
            ?? BookingInvoice::query()
                ->whereIn('booking_id', $bookingIds)
                ->where('is_paid', true)
                ->where('amount', '>', 0)
                ->orderBy('paid_date')
                ->orderBy('id')
                ->first();
    }

    private function createPendingCredit(RentingReferral $referral): RentingReferralPointLedger
    {
        $existing = $this->creditFor($referral);
        if ($existing) {
            if ($existing->status === RentingReferralPointLedger::STATUS_REVERSED) {
                $existing->update([
                    'status' => RentingReferralPointLedger::STATUS_PENDING,
                    'available_from' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                ]);
            }

            return $existing->fresh();
        }

        return RentingReferralPointLedger::query()->create([
            'customer_id' => $referral->referrer_customer_id,
            'referral_id' => $referral->id,
            'direction' => RentingReferralPointLedger::DIRECTION_CREDIT,
            'status' => RentingReferralPointLedger::STATUS_PENDING,
            'points' => RentingReferralSettings::pointsPerQualifiedReferral(),
        ]);
    }

    private function creditFor(RentingReferral $referral): ?RentingReferralPointLedger
    {
        return RentingReferralPointLedger::query()
            ->where('referral_id', $referral->id)
            ->where('direction', RentingReferralPointLedger::DIRECTION_CREDIT)
            ->first();
    }

    private function uniqueCode(): string
    {
        for ($i = 0; $i < 20; $i++) {
            $code = strtoupper(Str::random(8));
            if (! RentingReferral::query()->where('referral_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Could not issue a referral code.');
    }

    /** @param  array<string, mixed>|null  $old  @param  array<string, mixed>|null  $new */
    private function writeLog(RentingReferral $referral, string $action, ?array $old, ?array $new, ?int $userId = null): void
    {
        RentingReferralLog::query()->create([
            'referral_id' => $referral->id,
            'action' => $action,
            'old_data' => $old,
            'new_data' => $new,
            'changed_by' => $userId ?? $this->staffId(),
            'created_at' => now(),
        ]);
    }

    /** @return array<string, mixed> */
    private function snapshot(RentingReferral $referral): array
    {
        return [
            'status' => $referral->status,
            'referred_customer_id' => $referral->referred_customer_id,
            'matched_at' => optional($referral->matched_at)?->toDateTimeString(),
            'qualified_at' => optional($referral->qualified_at)?->toDateTimeString(),
            'review_reason' => $referral->review_reason,
            'warnings' => $referral->warnings,
        ];
    }

    private function staffId(): ?int
    {
        $user = FluxAdminAccess::user();

        return $user?->getAuthIdentifier() ? (int) $user->getAuthIdentifier() : null;
    }

    private function attachQualifyingInvoiceIfMissing(RentingReferral $referral): void
    {
        if ($referral->referred_qualifying_invoice_id || ! $referral->referred_customer_id || ! $referral->created_at) {
            return;
        }

        $referred = $referral->referred ?: Customer::query()->find($referral->referred_customer_id);
        if (! $referred) {
            return;
        }

        $invoice = $this->firstPaidWeeklyInvoiceForCustomer($referred, $referral->created_at);
        if (! $invoice) {
            return;
        }

        $referral->update([
            'referred_qualifying_booking_id' => $invoice->booking_id,
            'referred_qualifying_invoice_id' => $invoice->id,
            'qualified_at' => $referral->qualified_at ?? now(),
            'matched_at' => $referral->matched_at ?? $referral->created_at,
        ]);
    }

    private function restoreStatusFromCredit(RentingReferral $referral): void
    {
        $credit = $this->creditFor($referral);
        if (! $credit) {
            return;
        }

        if (in_array($credit->status, [
            RentingReferralPointLedger::STATUS_AVAILABLE,
            RentingReferralPointLedger::STATUS_REDEEMED,
        ], true) && $referral->status !== RentingReferral::STATUS_APPROVED) {
            $referral->update(['status' => RentingReferral::STATUS_APPROVED]);

            return;
        }

        if ($credit->status === RentingReferralPointLedger::STATUS_PENDING
            && $referral->status !== RentingReferral::STATUS_REVIEW) {
            $referral->update(['status' => RentingReferral::STATUS_REVIEW]);
        }
    }

    private function assertCanReview(?int $userId): void
    {
        $user = $userId ? \App\Models\User::query()->find($userId) : FluxAdminAccess::user();
        if (! RentingReferralAccess::canReview($user)) {
            throw new RuntimeException('You do not have permission to review rental referrals.');
        }
    }

    private function assertCanRedeem(?int $userId): void
    {
        $user = $userId ? \App\Models\User::query()->find($userId) : FluxAdminAccess::user();
        if (! RentingReferralAccess::canView($user)) {
            throw new RuntimeException('You do not have permission to apply a rental referral reward.');
        }
    }

    private function referralCreatedAfterBookingStart(RentingReferral $referral, ?RentingBooking $booking): bool
    {
        if (! $booking?->start_date || ! $referral->created_at) {
            return false;
        }

        return $referral->created_at->toDateString() > $booking->start_date->toDateString();
    }

    private function rewardTransactionTypeId(): int
    {
        $type = TransactionType::query()->where('type', RentingReferralSettings::transactionTypeName())->first();
        if (! $type) {
            $type = TransactionType::query()->create(['type' => RentingReferralSettings::transactionTypeName()]);
        }

        return (int) $type->id;
    }

    private function rewardPaymentMethodId(): int
    {
        $slug = (string) config('renting_referrals.payment_method_slug', 'cash');
        $method = PaymentMethod::query()->where('slug', $slug)->first()
            ?? PaymentMethod::query()->orderBy('id')->first();

        if (! $method) {
            throw new RuntimeException('No payment method is available for the referral reward.');
        }

        return (int) $method->id;
    }

    private function sendInvitation(RentingReferral $referral): void
    {
        $email = RentingReferralIdentity::email($referral->submitted_email);
        if (! $email) {
            return;
        }

        $this->safeMail(fn () => Mail::to($email)->send(new RentingReferralInvitationMail($referral)));
    }

    private function sendUnderReview(RentingReferral $referral): void
    {
        $email = RentingReferralIdentity::email($referral->referrer?->email);
        if (! $email) {
            return;
        }

        $this->safeMail(fn () => Mail::to($email)->send(new RentingReferralUnderReviewMail($referral)));
    }

    private function sendRewardAvailable(RentingReferral $referral): void
    {
        $email = RentingReferralIdentity::email($referral->referrer?->email);
        if (! $email) {
            return;
        }

        $this->safeMail(fn () => Mail::to($email)->send(new RentingReferralRewardAvailableMail($referral)));
    }

    private function sendApprovalReport(RentingReferral $referral, ?int $userId): void
    {
        $to = RentingReferralSettings::approvalReportTo();
        $this->safeMail(fn () => Mail::to($to)->send(new RentingReferralApprovalReportMail($referral, $userId)));
    }

    private function sendStaffInvoiceNotice(
        RentingReferral $referral,
        string $event,
        ?int $userId,
        ?BookingInvoice $invoice = null,
        ?RentingBooking $booking = null,
        ?RentingTransaction $transaction = null,
        ?float $amount = null,
        ?string $proof = null
    ): void {
        $to = RentingReferralSettings::approvalReportTo();
        $this->safeMail(fn () => Mail::to($to)->send(new RentingReferralStaffInvoiceMail(
            $referral,
            $event,
            $userId,
            $invoice,
            $booking,
            $transaction,
            $amount,
            $proof
        )));
    }

    private function sendFreeWeekPaymentReceipt(
        RentingBooking $booking,
        BookingInvoice $invoice,
        RentingTransaction $transaction,
        float $amount,
        bool $fromProgramme
    ): void {
        $methodId = (int) ($transaction->payment_method_id ?? 0);
        if ($methodId < 1) {
            return;
        }

        $note = $fromProgramme
            ? 'Applied! This user has redeemed a free week on this invoice through the rental referral programme.'
            : 'Applied! This user has redeemed a free week on this invoice through the rental referral programme (direct).';

        app(RentalBookingLifecycle::class)->sendPaidInvoiceReceipt(
            (int) $booking->id,
            $invoice,
            $transaction,
            $methodId,
            $amount,
            0.0,
            'Paid in full',
            'We have received your payment and this invoice is now marked as paid in full.',
            $note
        );
    }

    private function safeMail(callable $send): void
    {
        try {
            $send();
        } catch (Throwable $e) {
            Log::warning('renting_referral_mail_failed', ['message' => $e->getMessage()]);
        }
    }

    private function tablesReady(): bool
    {
        return Schema::hasTable('renting_referrals')
            && Schema::hasTable('renting_referral_point_ledger')
            && Schema::hasTable('renting_referral_logs');
    }

    private function guardTables(): void
    {
        if (! $this->tablesReady()) {
            throw new RuntimeException('Rental referral tables are not installed yet.');
        }
    }
}
