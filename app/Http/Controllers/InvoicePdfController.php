<?php

namespace App\Http\Controllers;

use App\Models\NgnDigitalInvoice;
use App\Support\AgreementPdfViewAssets;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function download(NgnDigitalInvoice $invoice)
    {
        $pdf = Pdf::loadView(
            'invoices.templates.modern',
            array_merge(AgreementPdfViewAssets::composerVariables(), compact('invoice'))
        )->setPaper('a4', 'portrait');

        return $pdf->download('invoice_'.$invoice->invoice_number.'.pdf');
    }

    public function print(NgnDigitalInvoice $invoice)
    {
        $pdf = Pdf::loadView(
            'invoices.templates.printable',
            array_merge(AgreementPdfViewAssets::composerVariables(), compact('invoice'))
        )->setPaper('a4', 'portrait');

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
