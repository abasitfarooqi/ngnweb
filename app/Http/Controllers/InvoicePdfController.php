<?php

namespace App\Http\Controllers;

use App\Models\NgnDigitalInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function download(NgnDigitalInvoice $invoice)
    {
        $pdf = PDF::loadView('invoices.templates.modern', compact('invoice'));

        return $pdf->download('invoice_'.$invoice->invoice_number.'.pdf');
    }

    public function print(NgnDigitalInvoice $invoice)
    {
        $pdf = PDF::loadView('invoices.templates.printable', compact('invoice'));

        return $pdf->stream('invoice_'.$invoice->invoice_number.'.pdf');
    }

    public function duplicate(NgnDigitalInvoice $invoice)
    {
        $newInvoice = $invoice->replicate();
        $newInvoice->invoice_number = NgnDigitalInvoice::generateNumber();
        $newInvoice->save();

        return redirect()->route('crud.ngn-digital-invoice.edit', $newInvoice->id);
    }
}
