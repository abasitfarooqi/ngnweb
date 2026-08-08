<?php

namespace App\Support;

use App\Models\FinanceApplication;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use Carbon\Carbon;
use Illuminate\View\View;

/** Resolve contract/start datetimes for agreement HTML + PDF (no midnight placeholders). */
final class AgreementDateTime
{
    public const SIGNATURE_DISPLAY_FORMAT = 'd-F-Y H:i';

    public static function hasMidnightTime(Carbon $dateTime): bool
    {
        return (int) $dateTime->format('His') === 0;
    }

    public static function resolve(mixed $value, ?Carbon $fallback = null): Carbon
    {
        $dateTime = Carbon::parse($value);

        if (! self::hasMidnightTime($dateTime)) {
            return $dateTime;
        }

        $fallback ??= now();

        return $dateTime->copy()->setTimeFromTimeString($fallback->format('H:i:s'));
    }

    public static function format(mixed $value, string $format = self::SIGNATURE_DISPLAY_FORMAT, ?Carbon $fallback = null): string
    {
        return self::resolve($value, $fallback)->format($format);
    }

    /** Display line for “Signature Date:” on finance + rental contracts. */
    public static function signatureDateDisplay(array $data): string
    {
        $format = self::SIGNATURE_DISPLAY_FORMAT;

        if (! empty($data['agreementStartDate'])) {
            return self::formatParsedInput($data['agreementStartDate'], $format);
        }

        if (! empty($data['contractStartDate'])) {
            return self::format($data['contractStartDate'], $format);
        }

        $booking = $data['booking'] ?? null;

        if ($booking instanceof FinanceApplication && ! empty($booking->contract_date)) {
            return self::format($booking->contract_date, $format);
        }

        if ($booking instanceof RentingBooking && ! empty($booking->start_date)) {
            return self::format($booking->start_date, $format);
        }

        if (! empty($data['today'])) {
            return self::formatParsedInput($data['today'], $format);
        }

        if ($booking && ! empty($booking->created_at)) {
            return self::format($booking->created_at, $format);
        }

        return self::format(now(), $format);
    }

    private static function formatParsedInput(mixed $value, string $format): string
    {
        if ($value instanceof \DateTimeInterface) {
            return self::resolve($value)->format($format);
        }

        if (is_object($value)) {
            return self::format($value, $format);
        }

        if (! is_scalar($value)) {
            return self::format(now(), $format);
        }

        $string = trim((string) $value);

        foreach (['d/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'] as $pattern) {
            try {
                return self::resolve(Carbon::createFromFormat($pattern, $string))->format($format);
            } catch (\Throwable) {
            }
        }

        return self::format($string, $format);
    }

    public static function prepareRentalBooking(RentingBooking $booking, bool $persist = false): RentingBooking
    {
        $current = Carbon::parse($booking->start_date);

        if (! self::hasMidnightTime($current)) {
            return $booking;
        }

        $resolved = self::resolve($current);
        $stamp = $resolved->format('Y-m-d H:i:s');
        $due = $resolved->copy()->addDays(7)->format('Y-m-d H:i:s');

        if ($persist) {
            $booking->update([
                'start_date' => $stamp,
                'due_date'   => $due,
            ]);
            RentingBookingItem::query()
                ->where('booking_id', $booking->id)
                ->update([
                    'start_date' => $stamp,
                    'due_date'   => $due,
                ]);
            $booking->refresh();
        } else {
            $booking->setAttribute('start_date', $resolved);
        }

        return $booking;
    }

    public static function prepareFinanceApplication(FinanceApplication $application, bool $persist = false): FinanceApplication
    {
        if (empty($application->contract_date)) {
            return $application;
        }

        $current = Carbon::parse($application->contract_date);

        if (! self::hasMidnightTime($current)) {
            return $application;
        }

        $resolved = self::resolve($current);
        $stamp = $resolved->format('Y-m-d H:i:s');

        if ($persist) {
            $application->update(['contract_date' => $stamp]);
            $application->refresh();
        } else {
            $application->setAttribute('contract_date', $resolved);
        }

        return $application;
    }

    public static function prepareViewData(View $view): void
    {
        $data = $view->getData();
        $updates = [];

        $booking = $data['booking'] ?? null;

        if ($booking instanceof RentingBooking) {
            $updates['booking'] = self::prepareRentalBooking($booking, false);
        } elseif ($booking instanceof FinanceApplication) {
            $updates['booking'] = self::prepareFinanceApplication($booking, false);
        }

        $merged = array_merge($data, $updates);
        $updates['signatureDate'] = self::signatureDateDisplay($merged);

        $view->with($updates);
    }

    public static function preparePdfData(array $data, bool $persist = true): array
    {
        $booking = $data['booking'] ?? null;

        if ($booking instanceof RentingBooking) {
            $data['booking'] = self::prepareRentalBooking($booking, $persist);
        } elseif ($booking instanceof FinanceApplication) {
            $data['booking'] = self::prepareFinanceApplication($booking, $persist);
        }

        $data['signatureDate'] = self::signatureDateDisplay($data);

        return $data;
    }

    public static function rentalStart(RentingBooking $booking): Carbon
    {
        return Carbon::parse(self::prepareRentalBooking($booking, false)->start_date);
    }

    public static function rentalEnd12Month(Carbon $start): Carbon
    {
        return $start->copy()->addMonths(12);
    }

    /** @return array{agreementStartDate: string, agreementEndDate: string} */
    public static function rentalTwelveMonthDateStrings(RentingBooking $booking): array
    {
        $start = self::rentalStart($booking);
        $end = self::rentalEnd12Month($start);

        return [
            'agreementStartDate' => $start->format('d/m/Y H:i'),
            'agreementEndDate' => $end->format('d/m/Y H:i'),
        ];
    }

    /** @return list<array{start: Carbon, end: Carbon}> */
    public static function rentalPcnSegments(Carbon $start): array
    {
        $end1 = $start->copy()->addMonths(5);
        $end2 = $end1->copy()->addMonths(5);
        $end3 = $end2->copy()->addMonths(5);

        return [
            ['start' => $start->copy(), 'end' => $end1],
            ['start' => $end1->copy(), 'end' => $end2],
            ['start' => $end2->copy(), 'end' => $end3],
        ];
    }
}
