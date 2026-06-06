<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use App\Models\CustomerDocument;
use App\Models\RentingBooking;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class DocumentsTab extends Component
{
    public int $bookingId;

    public ?string $docUploadLink = null;
    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function markDocumentsComplete(): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);

        if ($booking->state === 'Awaiting Documents & Payment') {
            $booking->update(['state' => 'Awaiting Payment']);
            $this->flashMessage = 'Documents marked as complete. Booking now awaiting payment.';
            $this->flashType = 'success';
        } elseif ($booking->state === 'Awaiting Documents') {
            $booking->update(['state' => 'Completed']);
            $this->flashMessage = 'Documents marked as complete. Booking state set to Completed.';
            $this->flashType = 'success';
        } else {
            $this->flashMessage = 'Documents are already confirmed for this booking (state: '.$booking->state.').';
            $this->flashType = 'info';
        }
    }

    public function generateDocumentLink(): void
    {
        $booking = RentingBooking::with('customer')->findOrFail($this->bookingId);

        if (! $booking->customer_id) {
            $this->flashMessage = 'No customer linked to this booking.';
            $this->flashType = 'error';

            return;
        }

        $this->docUploadLink = url('/generate-docs-upload-link-access/'.$booking->customer_id).'?booking_id='.$this->bookingId;
        $this->flashMessage = 'Document upload link generated. Share this link with the customer.';
        $this->flashType = 'success';
    }

    public function render()
    {
        $booking = RentingBooking::with('customer')->findOrFail($this->bookingId);

        $documents = CustomerDocument::with('documentType')
            ->where(function ($q) use ($booking) {
                $q->where('customer_id', $booking->customer_id)
                    ->orWhere('booking_id', $this->bookingId);
            })
            ->orderByDesc('created_at')
            ->get();

        $pendingInvoiceAmount = BookingInvoice::where('booking_id', $this->bookingId)
            ->where('is_paid', false)
            ->where('invoice_date', '<=', now())
            ->sum('amount');

        return view('flux-admin.partials.rentals.documents-tab', [
            'booking'              => $booking,
            'documents'            => $documents,
            'pendingInvoiceAmount' => $pendingInvoiceAmount,
        ]);
    }
}
