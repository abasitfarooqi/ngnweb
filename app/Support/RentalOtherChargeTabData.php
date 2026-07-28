<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class RentalOtherChargeTabData
{
    /** @return array<string, mixed>|null */
    public static function detail(int $chargeId, int $bookingId): ?array
    {
        $charge = DB::table('renting_other_charges as ROC')
            ->join('renting_bookings as RB', 'RB.id', '=', 'ROC.booking_id')
            ->join('customers as C', 'C.id', '=', 'RB.customer_id')
            ->leftJoin('renting_booking_items as RBI', function ($join) {
                $join->on('RBI.booking_id', '=', 'RB.id')
                    ->whereNull('RBI.end_date');
            })
            ->leftJoin('motorbikes as M', 'M.id', '=', 'RBI.motorbike_id')
            ->where('ROC.id', $chargeId)
            ->where('ROC.booking_id', $bookingId)
            ->select(
                'ROC.id',
                'ROC.booking_id',
                'ROC.description',
                'ROC.amount',
                'ROC.is_paid',
                'ROC.is_whatsapp_sent',
                'ROC.whatsapp_last_reminder_sent_at',
                'ROC.email_last_reminder_sent_at',
                'ROC.created_at',
                DB::raw("CONCAT(C.first_name, ' ', C.last_name) AS customer_name"),
                'C.email as customer_email',
                'C.whatsapp as customer_whatsapp',
                'C.phone as customer_phone',
                'M.reg_no as motorbike_reg_no',
            )
            ->first();

        if (! $charge) {
            return null;
        }

        $amount = (float) $charge->amount;
        $whatsappUrl = self::whatsappUrl(
            (string) ($charge->customer_whatsapp ?: $charge->customer_phone),
            (string) $charge->customer_name,
            (int) $charge->booking_id,
            (string) ($charge->motorbike_reg_no ?? ''),
            (string) $charge->description,
            $amount,
        );

        return [
            'id' => (int) $charge->id,
            'booking_id' => (int) $charge->booking_id,
            'description' => (string) $charge->description,
            'amount' => $amount,
            'is_paid' => (bool) $charge->is_paid,
            'is_whatsapp_sent' => (bool) $charge->is_whatsapp_sent,
            'whatsapp_last_reminder_sent_at' => $charge->whatsapp_last_reminder_sent_at,
            'email_last_reminder_sent_at' => $charge->email_last_reminder_sent_at,
            'created_at' => $charge->created_at,
            'customer_name' => $charge->customer_name,
            'customer_email' => $charge->customer_email,
            'customer_phone' => $charge->customer_phone,
            'customer_whatsapp' => $charge->customer_whatsapp,
            'motorbike_reg_no' => $charge->motorbike_reg_no,
            'whatsapp_url' => $whatsappUrl,
            'email_body' => self::reminderMessage(
                (string) $charge->customer_name,
                (int) $charge->booking_id,
                (string) ($charge->motorbike_reg_no ?? ''),
                (string) $charge->description,
                $amount,
            ),
        ];
    }

    public static function whatsappUrl(
        string $phone,
        string $customerName,
        int $bookingId,
        string $regNo,
        string $description,
        float $amount,
    ): string {
        $number = preg_replace('/\s+|^0/', '', $phone);
        $number = preg_replace('/^(\+44)+/', '', (string) $number);
        $number = preg_replace('/^44/', '', (string) $number);
        $number = '+44'.$number;
        $number = preg_replace('/\s+/', '', $number);

        $message = self::reminderMessage($customerName, $bookingId, $regNo, $description, $amount);

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }

    public static function reminderMessage(
        string $customerName,
        int $bookingId,
        string $regNo,
        string $description,
        float $amount,
    ): string {
        $regText = $regNo !== '' ? " for motorbike {$regNo}" : '';

        return "Dear {$customerName}, this is a reminder regarding an additional charge on rental booking #{$bookingId}{$regText}. "
            ."Charge: {$description}. Outstanding amount: £".number_format($amount, 2).'. '
            .'Please ensure payment is made as soon as possible to avoid further action. '
            .'If you have already paid, please contact us immediately at 0208 314 1498 or WhatsApp us on 07951790568, NGN Motors, '
            .self::staffSignature().'.';
    }

    private static function staffSignature(): string
    {
        $user = function_exists('backpack_auth') ? (backpack_auth()->user() ?: auth()->user()) : auth()->user();
        $staffId = optional($user)->id;
        $staffName = trim(((string) optional($user)->first_name).' '.((string) optional($user)->last_name));

        if ($staffName === '') {
            $staffName = (string) (optional($user)->name ?: 'Staff');
        }

        return $staffId ? "{$staffName} (ID: {$staffId})" : $staffName;
    }
}
