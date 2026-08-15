<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RentalInvoiceTabData
{
    public static function rows(int $bookingId): Collection
    {
        $latestTransactionIds = DB::table('renting_transactions')
            ->selectRaw('MAX(id) as latest_transaction_id, invoice_id')
            ->whereNotNull('invoice_id')
            ->groupBy('invoice_id');

        $invoicePaymentSums = DB::table('renting_transactions')
            ->selectRaw('invoice_id, SUM(amount) as total_paid_amount')
            ->whereNotNull('invoice_id')
            ->groupBy('invoice_id');

        return DB::table('booking_invoices as BI')
            ->leftJoinSub($latestTransactionIds, 'LRT', fn ($join) => $join->on('LRT.invoice_id', '=', 'BI.id'))
            ->leftJoinSub($invoicePaymentSums, 'IPS', fn ($join) => $join->on('IPS.invoice_id', '=', 'BI.id'))
            ->leftJoin('renting_transactions as RT', 'RT.id', '=', 'LRT.latest_transaction_id')
            ->leftJoin('users as U', 'RT.user_id', '=', 'U.id')
            ->select(
                'BI.id',
                'BI.invoice_date',
                'BI.amount',
                'BI.paid_date',
                'BI.state',
                'BI.deposit',
                'BI.is_paid',
                'BI.is_whatsapp_sent',
                'BI.whatsapp_last_reminder_sent_at',
                'RT.id as transaction_no',
                'RT.transaction_date',
                'RT.created_at as transaction_datetime',
                'U.first_name as received_by',
                DB::raw('COALESCE(IPS.total_paid_amount, 0) as total_paid_amount'),
                DB::raw('(BI.amount - COALESCE(IPS.total_paid_amount, 0)) as outstanding_balance'),
            )
            ->selectRaw('CASE WHEN DATE(BI.invoice_date) <= ? THEN 1 ELSE 0 END as is_due', [now()->toDateString()])
            ->where('BI.booking_id', $bookingId)
            ->where('BI.is_posted', 1)
            ->where('BI.amount', '>', 0)
            ->orderByDesc('BI.invoice_date')
            ->orderByDesc('BI.id')
            ->get();
    }

    /** @return array<string, mixed>|null */
    public static function detail(int $invoiceId): ?array
    {
        $invoice = DB::table('booking_invoices as BI')
            ->leftJoin('renting_bookings as RB', 'RB.id', '=', 'BI.booking_id')
            ->leftJoin('customers as C', 'C.id', '=', 'RB.customer_id')
            ->leftJoin('renting_booking_items as RBI', function ($join) {
                $join->on('RBI.booking_id', '=', 'RB.id')
                    ->whereNull('RBI.end_date');
            })
            ->leftJoin('motorbikes as M', 'M.id', '=', 'RBI.motorbike_id')
            ->select(
                'BI.id',
                'BI.invoice_date',
                'BI.amount',
                'BI.is_paid',
                'BI.is_whatsapp_sent',
                'BI.whatsapp_last_reminder_sent_at',
                DB::raw("CONCAT(C.first_name, ' ', C.last_name) AS customer_name"),
                'C.whatsapp as customer_whatsapp',
                'C.phone as customer_phone',
                'M.reg_no as motorbike_reg_no',
                'RBI.weekly_rent as weekly_rent',
            )
            ->where('BI.id', $invoiceId)
            ->first();

        if (! $invoice) {
            return null;
        }

        $whatsappUrl = self::whatsappUrl(
            (string) ($invoice->customer_whatsapp ?: $invoice->customer_phone),
            (string) $invoice->customer_name,
            (string) ($invoice->motorbike_reg_no ?? ''),
            (float) ($invoice->weekly_rent ?? 0),
            (string) $invoice->invoice_date,
        );

        return [
            'id' => (int) $invoice->id,
            'invoice_date' => $invoice->invoice_date,
            'amount' => (float) $invoice->amount,
            'is_paid' => (bool) $invoice->is_paid,
            'is_whatsapp_sent' => (bool) $invoice->is_whatsapp_sent,
            'whatsapp_last_reminder_sent_at' => $invoice->whatsapp_last_reminder_sent_at,
            'customer_name' => $invoice->customer_name,
            'customer_phone' => $invoice->customer_phone,
            'customer_whatsapp' => $invoice->customer_whatsapp,
            'motorbike_reg_no' => $invoice->motorbike_reg_no,
            'weekly_rent' => (float) ($invoice->weekly_rent ?? 0),
            'whatsapp_url' => $whatsappUrl,
        ];
    }

    public static function whatsappUrl(
        string $phone,
        string $customerName,
        string $regNo,
        float $weeklyRent,
        string $invoiceDate,
    ): string {
        $number = preg_replace('/\s+|^0/', '', $phone);
        $number = preg_replace('/^(\+44)+/', '', (string) $number);
        $number = preg_replace('/^44/', '', (string) $number);
        $number = '+44'.$number;
        $number = preg_replace('/\s+/', '', $number);

        $formattedDate = \Carbon\Carbon::parse($invoiceDate)->format('d M Y');
        $message = "Dear {$customerName}, this is a reminder regarding your Weekly Rental payment for motorbike {$regNo}. The outstanding amount of £"
            .number_format($weeklyRent, 2)." is due on {$formattedDate}. Please ensure payment is made as soon as possible to avoid late fees. If you have already paid, please contact us immediately at 0208 314 1498 or WhatsApp us on 07951790568, NGN Motors, "
            .self::staffSignature().'.';

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
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
