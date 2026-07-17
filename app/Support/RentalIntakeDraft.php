<?php

namespace App\Support;

use App\Models\BookingInvoice;
use App\Models\PaymentMethod;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingPricing;
use App\Support\AdminDateTimeInput;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class RentalIntakeDraft
{
    public const MAX_STEP = 6;

    /** @return array<string, mixed> */
    public function load(int $bookingId, int $userId): array
    {
        $booking = $this->findOwnedDraft($bookingId, $userId);
        $item = RentingBookingItem::query()
            ->where('booking_id', $booking->id)
            ->orderBy('id')
            ->first();
        $invoice = BookingInvoice::query()
            ->where('booking_id', $booking->id)
            ->orderBy('id')
            ->first();

        $meta = is_array($booking->intake_meta) ? $booking->intake_meta : [];

        return [
            'booking'          => $booking,
            'item'             => $item,
            'invoice'          => $invoice,
            'step'             => (int) ($booking->intake_step ?? 2),
            'motorbikeId'      => $item?->motorbike_id,
            'customerId'       => $booking->customer_id,
            'weeklyRent'       => $item ? (float) $item->weekly_rent : null,
            'deposit'          => (float) $booking->deposit,
            'startDate'        => AdminDateTimeInput::toLocal($booking->start_date),
            'notes'            => (string) ($booking->notes ?? ''),
            'termsAccepted'    => (bool) ($meta['terms_accepted'] ?? false),
            'paymentMethod'    => (string) ($meta['payment_method'] ?? 'cash'),
            'initialPayment'   => (float) ($meta['initial_payment'] ?? 0),
            'sendDocUploadLink'=> (bool) ($meta['send_doc_upload_link'] ?? true),
        ];
    }

    public function findResumableForUser(int $userId): ?RentingBooking
    {
        return RentingBooking::query()
            ->where('user_id', $userId)
            ->where('state', 'DRAFT')
            ->where('is_posted', false)
            ->whereNotNull('intake_step')
            ->where('intake_step', '<', self::MAX_STEP)
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->first();
    }

    /**
     * @param  array{
     *     motorbike_id:int,
     *     customer_id:int,
     *     start_date?:string,
     *     weekly_rent?:float|null,
     *     deposit?:float,
     *     notes?:string|null,
     *     terms_accepted?:bool,
     *     payment_method?:string,
     *     initial_payment?:float,
     *     send_doc_upload_link?:bool,
     * }  $data
     */
    public function persist(
        ?int $bookingId,
        int $userId,
        int $step,
        array $data,
    ): int {
        if ($step < 2 || $step > self::MAX_STEP) {
            throw new InvalidArgumentException('Intake step must be between 2 and '.self::MAX_STEP.'.');
        }

        return DB::transaction(function () use ($bookingId, $userId, $step, $data) {
            $start = AdminDateTimeInput::parseStart($data['start_date'] ?? null);
            $due = (clone $start)->addDays(7);
            $weeklyRent = $this->resolveWeeklyRent(
                (int) $data['motorbike_id'],
                isset($data['weekly_rent']) ? (float) $data['weekly_rent'] : null,
            );
            $deposit = (float) ($data['deposit'] ?? 0);
            $meta = [
                'terms_accepted'       => (bool) ($data['terms_accepted'] ?? false),
                'payment_method'       => (string) ($data['payment_method'] ?? 'cash'),
                'initial_payment'      => (float) ($data['initial_payment'] ?? 0),
                'send_doc_upload_link' => (bool) ($data['send_doc_upload_link'] ?? true),
            ];

            if ($bookingId) {
                $booking = $this->findOwnedDraft($bookingId, $userId);
                $booking->update([
                    'customer_id'  => $data['customer_id'],
                    'start_date'   => $start->format('Y-m-d H:i:s'),
                    'due_date'     => $due->format('Y-m-d H:i:s'),
                    'deposit'      => $deposit,
                    'notes'        => ($data['notes'] ?? '') !== '' ? $data['notes'] : null,
                    'intake_step'  => $step,
                    'intake_meta'  => $meta,
                ]);

                $item = RentingBookingItem::query()
                    ->where('booking_id', $booking->id)
                    ->orderBy('id')
                    ->first();

                if ($item) {
                    $item->update([
                        'motorbike_id' => $data['motorbike_id'],
                        'weekly_rent'  => $weeklyRent,
                        'start_date'   => $start->format('Y-m-d H:i:s'),
                        'due_date'     => $due->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    RentingBookingItem::create([
                        'booking_id'   => $booking->id,
                        'motorbike_id' => $data['motorbike_id'],
                        'user_id'      => $userId,
                        'weekly_rent'  => $weeklyRent,
                        'start_date'   => $start->format('Y-m-d H:i:s'),
                        'due_date'     => $due->format('Y-m-d H:i:s'),
                        'is_posted'    => false,
                    ]);
                }

                $invoice = BookingInvoice::query()
                    ->where('booking_id', $booking->id)
                    ->orderBy('id')
                    ->first();

                $invoiceAmount = $weeklyRent + ($deposit > 0 ? $deposit : 0);

                if ($invoice) {
                    $invoice->update([
                        'invoice_date' => $start->toDateString(),
                        'amount'       => $invoiceAmount,
                        'deposit'      => $deposit > 0 ? $deposit : 0,
                    ]);
                } else {
                    BookingInvoice::create([
                        'booking_id'   => $booking->id,
                        'user_id'      => $userId,
                        'invoice_date' => $start->toDateString(),
                        'amount'       => $invoiceAmount,
                        'deposit'      => $deposit > 0 ? $deposit : 0,
                        'state'        => 'weekly',
                        'is_posted'    => false,
                        'is_paid'      => false,
                    ]);
                }

                $this->syncInitialPayment($booking->id, $step, $meta);

                return $booking->id;
            }

            $booking = RentingBooking::create([
                'customer_id' => $data['customer_id'],
                'user_id'     => $userId,
                'start_date'  => $start->format('Y-m-d H:i:s'),
                'due_date'    => $due->format('Y-m-d H:i:s'),
                'state'       => 'DRAFT',
                'is_posted'   => false,
                'deposit'     => $deposit,
                'notes'       => ($data['notes'] ?? '') !== '' ? $data['notes'] : null,
                'intake_step' => $step,
                'intake_meta' => $meta,
            ]);

            RentingBookingItem::create([
                'booking_id'   => $booking->id,
                'motorbike_id' => $data['motorbike_id'],
                'user_id'      => $userId,
                'weekly_rent'  => $weeklyRent,
                'start_date'   => $start->format('Y-m-d H:i:s'),
                'due_date'     => $due->format('Y-m-d H:i:s'),
                'is_posted'    => false,
            ]);

            BookingInvoice::create([
                'booking_id'   => $booking->id,
                'user_id'      => $userId,
                'invoice_date' => $start->toDateString(),
                'amount'       => $weeklyRent + ($deposit > 0 ? $deposit : 0),
                'deposit'      => $deposit > 0 ? $deposit : 0,
                'state'        => 'weekly',
                'is_posted'    => false,
                'is_paid'      => false,
            ]);

            $this->syncInitialPayment($booking->id, $step, $meta);

            return $booking->id;
        });
    }

    /** @param  array<string, mixed>  $meta */
    private function syncInitialPayment(int $bookingId, int $step, array $meta): void
    {
        if ($step < 4) {
            return;
        }

        $invoice = BookingInvoice::query()
            ->where('booking_id', $bookingId)
            ->orderBy('id')
            ->first();

        if (! $invoice) {
            return;
        }

        $paymentMethod = (string) ($meta['payment_method'] ?? 'none');
        $initialPayment = (float) ($meta['initial_payment'] ?? 0);

        if ($paymentMethod === 'none' || $initialPayment <= 0) {
            if ($step >= 4) {
                $invoice->update(['is_posted' => true]);
            }

            return;
        }

        $alreadyReceived = (float) DB::table('renting_transactions')
            ->where('invoice_id', $invoice->id)
            ->sum('amount');

        $toRecord = round($initialPayment - $alreadyReceived, 2);

        if ($toRecord > 0) {
            $remaining = round((float) $invoice->amount - $alreadyReceived, 2);

            if ($toRecord > $remaining) {
                throw new InvalidArgumentException(
                    'Initial payment cannot exceed £'.number_format($remaining, 2).' outstanding on this invoice.'
                );
            }

            $methodId = $this->resolvePaymentMethodId($paymentMethod);

            if (! $methodId) {
                throw new RuntimeException('Selected payment method is not available. Check payment methods in admin.');
            }

            app(RentalBookingLifecycle::class)->recordPayment(
                $bookingId,
                (int) $invoice->id,
                $methodId,
                $toRecord
            );
        }

        $invoice->refresh();
        $invoice->update(['is_posted' => true]);
    }

    private function resolvePaymentMethodId(string $key): ?int
    {
        $titles = match ($key) {
            'cash' => ['Cash'],
            'card' => ['Card'],
            'bank' => ['Bank transfer', 'Bank Transfer', 'Bank'],
            default => [],
        };

        foreach ($titles as $title) {
            $id = PaymentMethod::query()
                ->where('is_enabled', true)
                ->where('title', $title)
                ->value('id');

            if ($id) {
                return (int) $id;
            }
        }

        if ($key === 'bank') {
            $id = PaymentMethod::query()
                ->where('is_enabled', true)
                ->where('title', 'like', '%Bank%')
                ->value('id');

            return $id ? (int) $id : null;
        }

        return null;
    }

    public function complete(int $bookingId, int $userId): void
    {
        $booking = $this->findOwnedDraft($bookingId, $userId);
        $booking->update([
            'intake_step' => self::MAX_STEP,
            'intake_meta' => null,
        ]);
    }

    public function discard(int $bookingId, int $userId): void
    {
        $booking = $this->findOwnedDraft($bookingId, $userId);
        app(RentalBookingLifecycle::class)->abortUnposted($booking);
    }

    private function findOwnedDraft(int $bookingId, int $userId): RentingBooking
    {
        $booking = RentingBooking::query()->find($bookingId);

        if (! $booking) {
            throw new RuntimeException('Draft booking not found.');
        }

        if ((int) $booking->user_id !== $userId) {
            throw new RuntimeException('You do not have access to this draft booking.');
        }

        if ($booking->is_posted || $booking->state !== 'DRAFT') {
            throw new RuntimeException('This booking is no longer an intake draft.');
        }

        return $booking;
    }

    private function resolveWeeklyRent(int $motorbikeId, ?float $weeklyRent): float
    {
        if ($weeklyRent !== null && $weeklyRent > 0) {
            return $weeklyRent;
        }

        $pricing = RentingPricing::query()
            ->where('motorbike_id', $motorbikeId)
            ->where('iscurrent', true)
            ->value('weekly_price');

        return $pricing !== null && (float) $pricing > 0 ? (float) $pricing : 0.0;
    }
}
