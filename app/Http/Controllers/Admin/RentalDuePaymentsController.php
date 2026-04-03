<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RentalDuePaymentsController extends Controller
{
    /**
     * Display a list of rental bookings that have an *unpaid* invoice due now or earlier.
     */
    public function index(Request $request)
    {
        // Subquery: latest *unpaid* invoice (is_paid = 0) with invoice_date <= now per booking.
        $latestDueInvoiceSub = BookingInvoice::select('booking_id', DB::raw('MAX(id) AS latest_invoice_id'))
            ->where('is_paid', 0)
            ->where('invoice_date', '<=', now())
            ->groupBy('booking_id');

        // Main query.
        $records = RentingBooking::query()
            ->select([
                'renting_bookings.id AS booking_no',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) AS customer"),
                'motorbikes.vin_number',
                'motorbikes.reg_no',
                'renting_booking_items.weekly_rent AS weekly',
                'booking_invoices.invoice_date',
                'booking_invoices.id AS invoice_id',
                'booking_invoices.is_whatsapp_sent',
                'booking_invoices.whatsapp_last_reminder_sent_at',
                'customers.whatsapp',
                'customers.phone',
                DB::raw("CASE 
                    WHEN booking_invoices.is_paid = 0 AND booking_invoices.invoice_date <= CURRENT_TIMESTAMP THEN 'Due'
                    WHEN booking_invoices.is_paid = 1 THEN 'Completed & Issued'
                    ELSE 'Pending'
                END AS state"),
            ])
            ->join('customers', 'customers.id', '=', 'renting_bookings.customer_id')
            ->join('renting_booking_items', 'renting_booking_items.booking_id', '=', 'renting_bookings.id')
            ->join('motorbikes', 'motorbikes.id', '=', 'renting_booking_items.motorbike_id')
            ->join('booking_invoices', 'booking_invoices.booking_id', '=', 'renting_bookings.id')
            ->where('renting_bookings.is_posted', 1)
            ->where('renting_booking_items.is_posted', 1)
            ->whereNull('renting_booking_items.end_date')
            ->where('booking_invoices.is_paid', 0)
            ->where('booking_invoices.invoice_date', '<=', now())
            ->orderBy('renting_bookings.id')
            ->get();

        $records = $records->map(function ($row) {
            $number = $row->whatsapp ?: $row->phone;
            $number = preg_replace('/\s+|^0/', '', $number);
            $number = preg_replace('/^(\+44)+/', '', $number);
            $number = preg_replace('/^44/', '', $number);
            $number = '+44'.$number;
            $number = preg_replace('/\s+/', '', $number);

            $message = "Dear {$row->customer}, this is a reminder regarding your Weekly Rental payment for motorbike {$row->reg_no}. The outstanding amount of £".number_format($row->weekly, 2).' is due on '.\Carbon\Carbon::parse($row->invoice_date)->format('d M Y').'. Please ensure payment is made as soon as possible to avoid late fees. If you have already paid, please contact us immediately at 0208 314 1498 or WhatsApp us on 07951790568, NGN Motors.';
            $row->whatsapp_number = $number;
            $row->whatsapp_url = "https://wa.me/{$number}?text=".urlencode($message);

            return $row;
        });

        return view('livewire.agreements.migrated.admin.rental_due_payments', [
            'title' => 'Rental Due Payments',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'RentalDuePayments' => false,
            ],
            'page' => 'resources/views/admin/rental_due_payments.blade.php',
            'controller' => 'app/Http/Controllers/Admin/RentalDuePaymentsController.php',
            'records' => $records,
        ]);
    }

    public function sendWhatsappReminder($invoiceId)
    {
        $invoice = BookingInvoice::findOrFail($invoiceId);
        $invoice->is_whatsapp_sent = true;
        $invoice->whatsapp_last_reminder_sent_at = now();
        $invoice->save();

        return redirect()->back()->with('success', 'WhatsApp reminder marked as sent.');
    }

    public function updateInvoiceDate(Request $request, $invoiceId)
    {
        $request->validate([
            'invoice_date' => 'required|date',
        ]);

        $invoice = BookingInvoice::findOrFail($invoiceId);
        $invoice->invoice_date = $request->invoice_date;
        $invoice->save();

        return redirect()->back()->with('success', 'Invoice date updated successfully.');
    }
}
