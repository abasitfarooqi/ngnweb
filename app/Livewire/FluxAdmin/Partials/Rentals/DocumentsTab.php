<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use App\Models\CustomerDocument;
use App\Models\RentingBooking;
use App\Support\RentalBookingLifecycle;
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

    public function activateRentalToday(): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);

        try {
            app(RentalBookingLifecycle::class)->activateRental($booking);
            app(RentalBookingLifecycle::class)->confirmDocuments($booking->fresh());
            $this->flashMessage = 'Documents received — rental activated for today.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function markDocumentsComplete(): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);
        app(RentalBookingLifecycle::class)->confirmDocuments($booking);
        $this->flashMessage = 'Document step confirmed. State: '.$booking->fresh()->state;
        $this->flashType = 'success';
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
        $lifecycle = app(RentalBookingLifecycle::class);
        $checklist = $lifecycle->documentChecklist($booking);
        $missing = $lifecycle->missingRequiredDocuments($booking);

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
            'checklist'            => $checklist,
            'missing'              => $missing,
            'lifecycleStatus'      => $lifecycle->lifecycleStatus($booking),
            'pendingInvoiceAmount' => $pendingInvoiceAmount,
        ]);
    }
}
