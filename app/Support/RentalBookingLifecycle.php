<?php

namespace App\Support;

use App\Mail\RentalAgreement;
use App\Mail\RentalPaymentReceipt;
use App\Mail\RentalPaymentReversedNotice;
use App\Models\AgreementAccess;
use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use App\Models\PaymentMethod;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingOtherCharge;
use App\Models\RentingOtherChargesTransaction;
use App\Models\RentingTransaction;
use App\Models\TransactionType;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class RentalBookingLifecycle
{
    public const STATUS_INTAKE = 'intake';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ENDED = 'ended';

    public function lifecycleStatus(RentingBooking $booking): string
    {
        $booking->loadMissing('rentingBookingItems');

        $hasActiveItem = $booking->rentingBookingItems->contains(
            fn (RentingBookingItem $item) => $item->end_date === null
        );

        if (! $hasActiveItem) {
            return self::STATUS_ENDED;
        }

        if (! $booking->is_posted) {
            return self::STATUS_INTAKE;
        }

        return self::STATUS_ACTIVE;
    }

    /** @return array<int, array{id:int, name:string, slug:string, approved:bool, status:string, document_id:?int, status_label:string, valid_until:?string, needs_upload:bool}> */
    public function documentChecklist(RentingBooking $booking): array
    {
        $booking->loadMissing('customer');

        $typesQuery = DocumentType::query()->orderBy('sort_order');

        if (Schema::hasColumn('document_types', 'is_mandatory') || Schema::hasColumn('document_types', 'is_required')) {
            $typesQuery->where(function ($q) {
                if (Schema::hasColumn('document_types', 'is_mandatory')) {
                    $q->where('is_mandatory', true);
                }
                if (Schema::hasColumn('document_types', 'is_required')) {
                    $q->orWhere('is_required', true);
                }
            });
        }

        if (Schema::hasColumn('document_types', 'required_for')) {
            $typesQuery->where(function ($q) {
                $q->whereJsonContains('required_for', 'rental')
                    ->orWhereNull('required_for');
            });
        }

        if (Schema::hasColumn('document_types', 'is_motorbike')) {
            $typesQuery->where('is_motorbike', false);
        }

        if (Schema::hasColumn('document_types', 'is_active')) {
            $typesQuery->where('is_active', true);
        }

        // Signed agreements are not customer upload slots (loyalty / rental agreement).
        $typesQuery->where(function ($q) {
            $q->whereNull('code')->orWhereNotIn('code', ['loyalty_scheme_policy', 'rental_agreement']);
        })->where(function ($q) {
            $q->where('name', '!=', 'Rental Agreement')
                ->where('name', '!=', 'Loyalty Scheme Policy');
        });

        $types = $typesQuery->get();

        $documentsByType = CustomerDocument::query()
            ->where('customer_id', $booking->customer_id)
            ->orderByDesc('id')
            ->get()
            ->unique('document_type_id')
            ->keyBy('document_type_id');

        return $types->map(function (DocumentType $type) use ($documentsByType) {
            $document = $documentsByType->get($type->id);
            $status = $this->resolveCustomerDocumentStatus($document);

            return [
                'id'            => $type->id,
                'name'          => $type->name,
                'slug'          => $type->slug,
                'approved'      => $status === 'approved',
                'status'        => $status,
                'status_label'  => $this->documentStatusLabel($status),
                'document_id'   => $document?->id,
                'valid_until'   => $document?->valid_until
                    ? Carbon::parse($document->valid_until)->toDateString()
                    : null,
                'needs_upload'  => $this->documentNeedsUpload($status),
            ];
        })->values()->all();
    }

    public function resolveCustomerDocumentStatus(?CustomerDocument $document): string
    {
        if (! $document || ! $document->file_path) {
            return 'missing';
        }

        $status = 'pending_review';

        if (Schema::hasColumn('customer_documents', 'status')) {
            $status = match ((string) $document->status) {
                'approved' => 'approved',
                'rejected' => 'rejected',
                'uploaded', 'pending_review' => 'pending_review',
                default => 'pending_review',
            };
        } else {
            $status = $document->is_verified ? 'approved' : 'pending_review';
        }

        // Approved but past valid_until → treat as expired (customer must renew).
        if ($status === 'approved' && $this->documentIsExpired($document)) {
            return 'expired';
        }

        return $status;
    }

    public function documentIsExpired(?CustomerDocument $document): bool
    {
        if (! $document?->valid_until) {
            return false;
        }

        try {
            return Carbon::parse($document->valid_until)->startOfDay()->lt(now()->startOfDay());
        } catch (\Throwable) {
            return false;
        }
    }

    public function documentNeedsUpload(string $status): bool
    {
        return in_array($status, ['missing', 'expired', 'rejected'], true);
    }

    public function documentStatusLabel(string $status): string
    {
        return match ($status) {
            'approved' => 'Approved',
            'rejected' => 'Re-upload requested',
            'pending_review' => 'Awaiting review',
            'expired' => 'Expired — re-upload',
            default => 'Not uploaded',
        };
    }

    public function setCustomerDocumentReviewStatus(CustomerDocument $document, string $status, ?string $rejectionReason = null): void
    {
        if (! in_array($status, ['approved', 'rejected', 'pending_review'], true)) {
            throw new InvalidArgumentException('Invalid document review status.');
        }

        $payload = [];

        if (Schema::hasColumn('customer_documents', 'status')) {
            $payload['status'] = $status;
        }

        if (Schema::hasColumn('customer_documents', 'is_verified')) {
            $payload['is_verified'] = $status === 'approved';
        }

        if (Schema::hasColumn('customer_documents', 'rejection_reason')) {
            $payload['rejection_reason'] = $status === 'rejected' ? $rejectionReason : null;
        }

        if (Schema::hasColumn('customer_documents', 'reviewer_id') && auth()->check()) {
            $payload['reviewer_id'] = auth()->id();
        }

        if (Schema::hasColumn('customer_documents', 'reviewed_at') && in_array($status, ['approved', 'rejected'], true)) {
            $payload['reviewed_at'] = now();
        }

        if ($payload === []) {
            throw new RuntimeException('This database cannot store document review status yet.');
        }

        $document->update($payload);

        if ($status === 'rejected') {
            app(CustomerDocumentReviewNotifier::class)->notifyCustomer($document->fresh(), $status);
        }

        if ($status === 'rejected' && $document->customer_id) {
            app(CustomerDocumentReviewNotifier::class)->clearStaffMandatorySubmittedFlag(
                (int) $document->customer_id,
                $document->booking_id ? (int) $document->booking_id : null
            );
        }
    }

    /**
     * Documents that still block rental activation: missing, expired, rejected, or awaiting review.
     *
     * @return list<string>
     */
    public function missingRequiredDocuments(RentingBooking $booking): array
    {
        return collect($this->documentChecklist($booking))
            ->reject(fn (array $row) => $row['status'] === 'approved')
            ->pluck('name')
            ->values()
            ->all();
    }

    /**
     * Customer action items for portal / upload link (upload optional expiry; re-upload when expired).
     *
     * @return list<string>
     */
    public function documentsNeedingCustomerUpload(RentingBooking|Customer $owner): array
    {
        if ($owner instanceof RentingBooking) {
            return collect($this->documentChecklist($owner))
                ->filter(fn (array $row) => $row['needs_upload'] ?? false)
                ->pluck('name')
                ->values()
                ->all();
        }

        $proxy = new RentingBooking(['customer_id' => $owner->id]);
        $proxy->setRelation('customer', $owner);

        return collect($this->documentChecklist($proxy))
            ->filter(fn (array $row) => $row['needs_upload'] ?? false)
            ->pluck('name')
            ->values()
            ->all();
    }

    public function activateRental(RentingBooking $booking, bool $force = false): array
    {
        if ($booking->is_posted) {
            throw new RuntimeException('This rental is already active.');
        }

        $missing = $this->missingRequiredDocuments($booking);
        if (! $force && $missing !== []) {
            throw new RuntimeException('Missing approved documents: '.implode(', ', $missing));
        }

        return DB::transaction(function () use ($booking) {
            $today = now()->startOfDay();
            $due = (clone $today)->addDays(7);

            $hasUnpaid = BookingInvoice::where('booking_id', $booking->id)
                ->where('is_paid', false)
                ->exists();

            $state = $hasUnpaid ? 'Awaiting Payment' : 'Completed';
            if ($booking->state === 'DRAFT' || $booking->state === null || $booking->state === '') {
                $state = $hasUnpaid ? 'Awaiting Documents & Payment' : 'Awaiting Documents';
            }

            $booking->update([
                'start_date' => $today->toDateString(),
                'due_date'   => $due->toDateString(),
                'state'      => $state,
                'is_posted'  => true,
            ]);

            RentingBookingItem::where('booking_id', $booking->id)->update([
                'start_date' => $today->toDateString(),
                'due_date'   => $due->toDateString(),
                'is_posted'  => true,
            ]);

            foreach (BookingInvoice::where('booking_id', $booking->id)->get() as $invoice) {
                $invoice->update([
                    'is_posted' => true,
                    'state'     => $invoice->is_paid ? 'Completed' : 'Awaiting Payment',
                ]);
            }

            $booking->refresh();

            return [
                'booking_id' => $booking->id,
                'state'      => $booking->state,
                'is_posted'  => true,
                'message'    => 'Rental activated for today.',
            ];
        });
    }

    public function recordPayment(
        int $bookingId,
        int $invoiceId,
        int $paymentMethodId,
        float $amountReceived
    ): array {
        if ($amountReceived <= 0) {
            throw new InvalidArgumentException('Invalid amount received.');
        }

        return DB::transaction(function () use ($bookingId, $invoiceId, $paymentMethodId, $amountReceived) {
            $booking = RentingBooking::findOrFail($bookingId);
            $invoice = BookingInvoice::where('booking_id', $bookingId)
                ->where('id', $invoiceId)
                ->firstOrFail();

            $pendingInvoices = BookingInvoice::where('booking_id', $bookingId)
                ->where(fn ($q) => $q->where('is_paid', false)->orWhere('is_posted', false))
                ->count();

            if ($pendingInvoices === 0) {
                throw new RuntimeException('No pending invoices found for this booking.');
            }

            if ($invoice->is_paid) {
                throw new RuntimeException('This invoice is already marked as paid.');
            }

            if ($invoice->amount <= 0) {
                throw new RuntimeException('Invoice amount is zero or negative.');
            }

            $totalPayableDue = (float) $invoice->amount;
            $totalReceived = (float) ($booking->transactions($invoice->id)->sum('amount') ?? 0);
            $remaining = $totalPayableDue - $totalReceived;

            if ($amountReceived > $remaining) {
                throw new RuntimeException('Amount received is greater than the remaining balance.');
            }

            RentingBookingItem::where('booking_id', $bookingId)->update(['is_posted' => true]);

            $transaction = RentingTransaction::create([
                'transaction_date'    => now(),
                'booking_id'          => $bookingId,
                'invoice_id'          => $invoice->id,
                'transaction_type_id' => 7,
                'payment_method_id'   => $paymentMethodId,
                'amount'              => $amountReceived,
                'user_id'             => auth()->id(),
                'notes'               => $remaining == $amountReceived ? 'Full payment received' : 'Partial payment received',
            ]);

            if ($remaining == $amountReceived) {
                $invoice->update([
                    'is_paid'    => true,
                    'is_posted'  => true,
                    'paid_date'  => now(),
                    'state'      => 'Completed',
                    'notes'      => 'Invoice paid in full',
                ]);

                $this->applyPaymentStateTransition($bookingId);
                $this->sendPaymentReceipt($bookingId, $invoice, $transaction, $paymentMethodId, $amountReceived, 0.0, 'Paid in full', 'We have received your payment and this invoice is now marked as paid in full.');
            } else {
                $invoice->update([
                    'is_paid' => false,
                    'state'   => 'Awaiting Payment',
                ]);

                $balance = max($remaining - $amountReceived, 0.0);
                $this->sendPaymentReceipt($bookingId, $invoice, $transaction, $paymentMethodId, $amountReceived, $balance, 'Part-paid', 'We have received your payment, but there is still an outstanding balance remaining on this invoice.');
            }

            $booking->refresh();

            return [
                'success'        => true,
                'transaction_id' => $transaction->id,
                'state'          => $booking->state,
                'is_posted'      => $booking->is_posted,
                'balance'        => max($remaining - $amountReceived, 0.0),
                'message'        => 'Payment recorded successfully.',
            ];
        });
    }

    public function reversePayment(BookingInvoice $invoice, ?int $auditUserId = null): array
    {
        return DB::transaction(function () use ($invoice, $auditUserId) {
            $invoice->loadMissing('booking');
            $latestTransaction = RentingTransaction::where('invoice_id', $invoice->id)
                ->orderByDesc('id')
                ->first();

            if (! $latestTransaction) {
                throw new RuntimeException('No payment transaction found for this invoice.');
            }

            $booking = $invoice->booking;
            $customer = Customer::findOrFail($booking->customer_id);
            $reversedAmount = (float) $latestTransaction->amount;
            $reversedId = $latestTransaction->id;
            $latestTransaction->delete();

            $remainingPaid = (float) RentingTransaction::where('invoice_id', $invoice->id)->sum('amount');

            $invoice->update([
                'is_paid'    => false,
                'paid_date'  => null,
                'state'      => 'Awaiting Payment',
                'notes'      => 'Payment reversed by staff'.($auditUserId ? ' (user ID: '.$auditUserId.')' : ''),
            ]);

            Log::info('rental_invoice_payment_reversed', [
                'invoice_id' => $invoice->id,
                'booking_id' => $booking->id,
                'deleted_transaction_id' => $reversedId,
                'deleted_transaction_amount' => $reversedAmount,
            ]);

            try {
                Mail::to([$customer->email, 'customerservice@neguinhomotors.co.uk'])->send(new RentalPaymentReversedNotice([
                    'email'                => [$customer->email, 'customerservice@neguinhomotors.co.uk'],
                    'customer_name'        => trim($customer->first_name.' '.$customer->last_name),
                    'weekly_rent'          => number_format((float) $invoice->amount, 2),
                    'invoice_amount'       => (float) $invoice->amount,
                    'registration_number'  => optional($booking->rentingBookingItems()->with('motorbike')->first()?->motorbike)->reg_no,
                    'invoice_date'         => $invoice->invoice_date,
                    'invoice_id'           => $invoice->id,
                    'booking_id'           => $booking->id,
                    'reversed_amount'      => number_format($reversedAmount, 2),
                ]));
            } catch (Exception $e) {
                Log::error('Failed to send reverse payment email: '.$e->getMessage());
            }

            return [
                'success'              => true,
                'invoice_id'           => $invoice->id,
                'deleted_transaction_id' => $reversedId,
                'remaining_paid'       => $remainingPaid,
                'message'              => 'Invoice payment reversed successfully.',
            ];
        });
    }

    /**
     * Outstanding amounts that matter when ending a rental.
     * Future invoices (invoice_date after the cut-off) are excluded — they are extras to purge.
     *
     * @return array{rental: float, additional: float, pcn: float, total: float, as_of: string}
     */
    public function closingPendings(RentingBooking $booking, ?string $asOfDate = null): array
    {
        $asOf = Carbon::parse($asOfDate ?? now())->toDateString();
        $booking->loadMissing(['rentingBookingItems']);

        $invoicePaymentSums = DB::table('renting_transactions')
            ->selectRaw('invoice_id, SUM(amount) as total_paid_amount')
            ->whereNotNull('invoice_id')
            ->groupBy('invoice_id');

        $rental = (float) DB::table('booking_invoices as BI')
            ->leftJoinSub($invoicePaymentSums, 'IPS', fn ($join) => $join->on('IPS.invoice_id', '=', 'BI.id'))
            ->where('BI.booking_id', $booking->id)
            ->whereDate('BI.invoice_date', '<=', $asOf)
            ->whereRaw('(BI.amount - COALESCE(IPS.total_paid_amount, 0)) > 0')
            ->selectRaw('SUM(BI.amount - COALESCE(IPS.total_paid_amount, 0)) as outstanding')
            ->value('outstanding');

        $additional = (float) RentingOtherCharge::query()
            ->where('booking_id', $booking->id)
            ->where(function ($q) {
                $q->where('is_paid', false)->orWhereNull('is_paid');
            })
            ->sum('amount');

        $pcn = 0.0;
        $motorbikeId = $booking->rentingBookingItems->firstWhere('motorbike_id')?->motorbike_id
            ?? $booking->rentingBookingItems->first()?->motorbike_id;

        if ($motorbikeId) {
            $pcnQuery = PcnCase::query()
                ->where('motorbike_id', $motorbikeId)
                ->where(fn ($q) => $q->where('isClosed', false)->orWhereNull('isClosed'));

            if ($booking->customer_id) {
                $pcnQuery->where('customer_id', $booking->customer_id);
            }

            $pcn = (float) $pcnQuery->sum(
                Schema::hasColumn('pcn_cases', 'reduced_amount') ? 'reduced_amount' : 'full_amount'
            );
        }

        return [
            'rental' => round($rental, 2),
            'additional' => round($additional, 2),
            'pcn' => round($pcn, 2),
            'total' => round($rental + $additional + $pcn, 2),
            'as_of' => $asOf,
        ];
    }

    /**
     * Delete unpaid future invoices after the collect/end date (no payment history).
     */
    public function purgeFutureInvoices(int $bookingId, string $collectDate): int
    {
        $cutOff = Carbon::parse($collectDate)->toDateString();

        $invoiceIdsWithTransactions = RentingTransaction::query()
            ->where('booking_id', $bookingId)
            ->whereNotNull('invoice_id')
            ->pluck('invoice_id')
            ->unique()
            ->all();

        $deleted = 0;

        BookingInvoice::query()
            ->where('booking_id', $bookingId)
            ->where('is_paid', false)
            ->whereNull('paid_date')
            ->whereDate('invoice_date', '>', $cutOff)
            ->when($invoiceIdsWithTransactions !== [], fn ($q) => $q->whereNotIn('id', $invoiceIdsWithTransactions))
            ->get()
            ->each(function (BookingInvoice $invoice) use (&$deleted) {
                $invoice->delete();
                $deleted++;
            });

        return $deleted;
    }

    public function endRental(
        RentingBooking $booking,
        RentingBookingItem $bookingItem,
        array $closingData,
        bool $proceedAnyway = false
    ): BookingClosing {
        return DB::transaction(function () use ($booking, $bookingItem, $closingData, $proceedAnyway) {
            $user = function_exists('backpack_user') ? backpack_user() : auth()->user();
            $userId = $user?->id ?? auth()->id();

            $collectDate = $closingData['collect_date'] ?? now()->toDateString();
            $collectTime = $closingData['collect_time'] ?? now()->format('H:i');
            $details = trim((string) ($closingData['collect_details'] ?? ''));

            if ($userId && ! str_contains($details, 'Ended by user #')) {
                $name = trim((string) ($user->name ?? $user->full_name ?? (($user->first_name ?? '').' '.($user->last_name ?? ''))));
                $stamp = sprintf('Ended by user #%s%s', $userId, $name !== '' ? " ({$name})" : '');
                $details = $details === '' ? $stamp : $details.' | '.$stamp;
            }

            $updateData = [
                'collect_details' => $details !== '' ? $details : null,
                'collect_date' => $collectDate,
                'collect_time' => $collectTime,
                'collect_checked' => (bool) ($closingData['collect_checked'] ?? false),
            ];

            if ($proceedAnyway) {
                $updateData['collect_proceeded_anyway_user_id'] = $userId;
                $updateData['collect_proceeded_anyway_at'] = now();
            }

            $closing = BookingClosing::updateOrCreate(
                ['booking_id' => $booking->id],
                $updateData
            );

            $bookingItem->update(['end_date' => $collectDate]);
            $this->purgeFutureInvoices($booking->id, $collectDate);
            $booking->touch();

            return $closing->fresh();
        });
    }

    public function abortUnposted(RentingBooking $booking): void
    {
        if ($booking->is_posted) {
            throw new RuntimeException('Cannot abort a posted rental. Use the closing workflow to end it.');
        }

        $hasTransactions = RentingTransaction::where('booking_id', $booking->id)->exists();
        if ($hasTransactions) {
            throw new RuntimeException('Cannot abort: payment transactions exist for this intake.');
        }

        DB::transaction(function () use ($booking) {
            BookingInvoice::where('booking_id', $booking->id)->delete();
            RentingBookingItem::where('booking_id', $booking->id)->delete();
            BookingClosing::where('booking_id', $booking->id)->delete();
            $booking->delete();
        });
    }

    public function payOtherCharge(int $chargeId, ?int $paymentMethodId = null): array
    {
        return DB::transaction(function () use ($chargeId, $paymentMethodId) {
            $otherCharge = RentingOtherCharge::findOrFail($chargeId);

            if ((bool) $otherCharge->getRawOriginal('is_paid')) {
                throw new RuntimeException('Charge is already paid.');
            }

            $otherCharge->update(['is_paid' => true]);

            $transactionType = TransactionType::where('type', 'Damage Fee')->firstOrFail();
            $paymentMethod = $paymentMethodId
                ? PaymentMethod::findOrFail($paymentMethodId)
                : PaymentMethod::where('title', 'Cash')->firstOrFail();

            $transaction = RentingOtherChargesTransaction::create([
                'transaction_date'    => now(),
                'charges_id'          => $otherCharge->id,
                'transaction_type_id' => $transactionType->id,
                'payment_method_id'   => $paymentMethod->id,
                'amount'              => $otherCharge->amount,
                'user_id'             => auth()->id(),
                'notes'               => 'Other charge paid',
            ]);

            return [
                'success'     => true,
                'transaction' => $transaction,
                'message'     => 'Charge paid successfully.',
            ];
        });
    }

    public function generateAgreementAccess(int $customerId, int $bookingId): array
    {
        $rentingBooking = RentingBooking::with('customer')->findOrFail($bookingId);

        if ((int) $rentingBooking->customer_id !== $customerId) {
            throw new RuntimeException('Customer does not match this booking.');
        }

        $passcode = Str::random(12);
        $expiresAt = now()->addDay();

        AgreementAccess::create([
            'customer_id' => $customerId,
            'booking_id'  => $bookingId,
            'passcode'    => $passcode,
            'expires_at'  => $expiresAt,
        ]);

        $url = AgreementAccess::customerSigningUrl($customerId, $passcode);

        $qrBase64 = '';
        try {
            $qrBase64 = QrCodeGenerator::dataUrl($url, 200);
        } catch (Exception $e) {
            Log::error('QR generation failed: '.$e->getMessage());
        }

        $customer = $rentingBooking->customer;
        if ($customer?->email) {
            try {
                Mail::to([$customer->email, 'customerservice@neguinhomotors.co.uk'])->send(new RentalAgreement([
                    'title' => 'Rental Agreement',
                    'body'  => 'Dear valued customer, please review and sign your rental agreement: '.$url,
                    'url'   => $url,
                ]));
            } catch (Exception $e) {
                Log::error('Agreement email failed: '.$e->getMessage());
            }
        }

        return [
            'url'     => $url,
            'qrImage' => $qrBase64,
            'message' => 'Agreement link generated and email sent if possible.',
        ];
    }

    public function confirmDocuments(RentingBooking $booking): array
    {
        if ($booking->state === 'Awaiting Documents & Payment') {
            $booking->update(['state' => 'Awaiting Payment']);
        } elseif ($booking->state === 'Awaiting Documents') {
            $booking->update(['state' => 'Completed']);
        }

        return ['state' => $booking->fresh()->state];
    }

    private function applyPaymentStateTransition(int $bookingId): void
    {
        $postedCount = BookingInvoice::where('booking_id', $bookingId)
            ->where('is_posted', true)
            ->count();

        $rentingBooking = RentingBooking::findOrFail($bookingId);

        if ($postedCount > 1) {
            if ($rentingBooking->state === 'Awaiting Documents & Payment') {
                $rentingBooking->state = 'Awaiting Documents';
                $rentingBooking->due_date = Carbon::parse($rentingBooking->due_date)->addWeek();
            } elseif ($rentingBooking->state === 'Awaiting Payment') {
                $rentingBooking->state = 'Completed';
                $rentingBooking->due_date = Carbon::parse($rentingBooking->due_date)->addWeek();
            } else {
                $rentingBooking->due_date = Carbon::parse($rentingBooking->due_date)->addWeek();
            }
        } elseif ($postedCount === 1) {
            if ($rentingBooking->state === 'Awaiting Documents & Payment') {
                $rentingBooking->state = 'Awaiting Documents';
            } elseif ($rentingBooking->state === 'Awaiting Payment') {
                $rentingBooking->state = 'Completed';
            }
        }

        $rentingBooking->save();
    }

    private function sendPaymentReceipt(
        int $bookingId,
        BookingInvoice $invoice,
        RentingTransaction $transaction,
        int $paymentMethodId,
        float $amountReceived,
        float $remainingBalance,
        string $statusLabel,
        string $receiptMessage
    ): void {
        try {
            $booking = RentingBooking::findOrFail($bookingId);
            $customer = Customer::findOrFail($booking->customer_id);
            $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);

            Mail::to([$customer->email, 'customerservice@neguinhomotors.co.uk'])->send(new RentalPaymentReceipt([
                'email'                 => [$customer->email, 'customerservice@neguinhomotors.co.uk'],
                'title'                 => 'Hire Payment Receipt',
                'body'                  => 'Find your payment details:',
                'booking_id'            => $bookingId,
                'invoice_id'            => $invoice->id,
                'invoice_date'          => $invoice->invoice_date,
                'transaction_id'        => $transaction->id,
                'transaction_date'      => $transaction->transaction_date,
                'payment_method'        => $paymentMethod->title,
                'amount'                => $amountReceived,
                'customer_name'         => trim($customer->first_name.' '.$customer->last_name),
                'registration_number'   => optional($booking->rentingBookingItems()->with('motorbike')->first()?->motorbike)->reg_no,
                'invoice_amount'        => (float) $invoice->amount,
                'remaining_balance'     => $remainingBalance,
                'invoice_status_label'  => $statusLabel,
                'receipt_message'       => $receiptMessage,
            ]));
        } catch (Exception $e) {
            Log::error('Failed to send payment receipt: '.$e->getMessage());
        }
    }
}
