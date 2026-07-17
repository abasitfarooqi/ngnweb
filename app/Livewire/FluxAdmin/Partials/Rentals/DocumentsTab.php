<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use App\Models\CustomerDocument;
use App\Models\RentingBooking;
use App\Support\DocumentUploadAccessGenerator;
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

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;
        $booking = RentingBooking::query()->find($bookingId);

        if ($booking?->customer_id) {
            $this->docUploadLink = app(DocumentUploadAccessGenerator::class)
                ->findActiveLink((int) $booking->customer_id, $bookingId);
        }
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
        $lifecycle = app(RentalBookingLifecycle::class);

        if (! $lifecycle->documentsPhasePending($booking)) {
            $this->flashMessage = 'Documents already completed. Current state: '.($booking->state ?: 'Unknown');
            $this->flashType = 'info';

            return;
        }

        $missing = $lifecycle->missingRequiredDocuments($booking);
        if ($missing !== []) {
            $this->flashMessage = 'Still missing approved documents: '.implode(', ', $missing).'. Approve each upload below, or use Documents completed (WhatsApp).';
            $this->flashType = 'error';

            return;
        }

        $result = $lifecycle->confirmDocuments($booking);
        $message = $result['state'] === 'DRAFT'
            ? 'Documents marked complete. Use Activate rental when ready.'
            : 'Documents completed. State is now: '.$result['state'].'. Next: payments (if needed) → Agreement → Issuance.';
        $this->flashMessage = $message;
        $this->flashType = 'success';
        $this->dispatch('rental-updated');
    }

    public function approveDocumentViaWhatsapp(int $documentTypeId): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);

        try {
            app(RentalBookingLifecycle::class)->approveDocumentViaWhatsapp($booking, $documentTypeId);
            $this->flashMessage = 'Document marked as verified via WhatsApp.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function markDocumentsCompleteViaWhatsapp(): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);
        $lifecycle = app(RentalBookingLifecycle::class);

        if (! $lifecycle->documentsPhasePending($booking)) {
            $this->flashMessage = 'Documents already completed. Current state: '.($booking->state ?: 'Unknown');
            $this->flashType = 'info';

            return;
        }

        try {
            $approved = $lifecycle->approveAllMandatoryDocumentsViaWhatsapp($booking);
            $result = $lifecycle->confirmDocuments($booking->fresh());
            $this->flashMessage = $result['state'] === 'DRAFT'
                ? "Documents verified via WhatsApp ({$approved} item(s)). Use Activate rental when ready."
                : ($approved > 0
                    ? "Documents verified via WhatsApp ({$approved} item(s)). State is now: {$result['state']}."
                    : 'Documents already approved — state is now: '.$result['state'].'.');
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function generateDocumentLink(bool $forceNew = false): void
    {
        $booking = RentingBooking::with('customer')->findOrFail($this->bookingId);

        if (! $booking->customer_id) {
            $this->flashMessage = 'No customer linked to this booking.';
            $this->flashType = 'error';

            return;
        }

        try {
            $result = app(DocumentUploadAccessGenerator::class)->create(
                (int) $booking->customer_id,
                $this->bookingId,
                sendEmail: false,
                forceNew: $forceNew,
            );
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';

            return;
        }

        $this->docUploadLink = $result['uploadLink'];
        $this->flashMessage = $result['reused']
            ? 'Active customer upload link restored (valid '.DocumentUploadAccessGenerator::LINK_VALID_DAYS.' days).'
            : 'New customer upload link created (valid '.DocumentUploadAccessGenerator::LINK_VALID_DAYS.' days).';
        $this->flashType = 'success';
    }

    public function approveDocument(int $documentId): void
    {
        $this->reviewDocument($documentId, 'approved', 'Document approved.');
    }

    public function requestReupload(int $documentId): void
    {
        $this->reviewDocument($documentId, 'rejected', 'Re-upload requested — customer can replace this document.');
    }

    public function markPendingReview(int $documentId): void
    {
        $this->reviewDocument($documentId, 'pending_review', 'Document marked as awaiting review.');
    }

    private function reviewDocument(int $documentId, string $status, string $message): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);
        $document = CustomerDocument::query()
            ->where('id', $documentId)
            ->where('customer_id', $booking->customer_id)
            ->firstOrFail();

        try {
            app(RentalBookingLifecycle::class)->setCustomerDocumentReviewStatus($document, $status);
            $this->flashMessage = $message;
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function setProfileEditingUnlocked(bool $unlocked): void
    {
        $booking = RentingBooking::with('customer')->findOrFail($this->bookingId);
        if (! $booking->customer) {
            $this->flashMessage = 'No customer linked to this booking.';
            $this->flashType = 'error';

            return;
        }

        $booking->customer->update(['profile_editing_unlocked' => $unlocked]);
        $this->flashMessage = $unlocked
            ? 'Customer may edit their profile again.'
            : 'Customer profile editing locked.';
        $this->flashType = 'success';
    }

    public function setDocumentReuploadUnlocked(bool $unlocked): void
    {
        $booking = RentingBooking::with('customer')->findOrFail($this->bookingId);
        if (! $booking->customer) {
            $this->flashMessage = 'No customer linked to this booking.';
            $this->flashType = 'error';

            return;
        }

        $booking->customer->update(['document_reupload_unlocked' => $unlocked]);
        $this->flashMessage = $unlocked
            ? 'Customer may replace approved documents.'
            : 'Approved document re-upload locked.';
        $this->flashType = 'success';
    }

    public function render()
    {
        $booking = RentingBooking::with('customer')->findOrFail($this->bookingId);
        $lifecycle = app(RentalBookingLifecycle::class);
        $checklist = $lifecycle->documentChecklist($booking);
        $missing = $lifecycle->missingRequiredDocuments($booking);

        $documents = CustomerDocument::with('documentType')
            ->where('customer_id', $booking->customer_id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (CustomerDocument $doc) use ($lifecycle) {
                $doc->review_status = $lifecycle->resolveCustomerDocumentStatus($doc);
                $doc->review_status_label = $lifecycle->documentStatusLabel($doc->review_status, $doc);
                $doc->verified_via_whatsapp = $lifecycle->documentVerifiedViaWhatsapp($doc);

                return $doc;
            });

        $pendingInvoiceAmount = BookingInvoice::where('booking_id', $this->bookingId)
            ->where('is_paid', false)
            ->where('invoice_date', '<=', now())
            ->sum('amount');

        $pendingReviewCount = $documents->where('review_status', 'pending_review')->count();
        $newUploadCount = $documents->filter(function (CustomerDocument $doc) {
            if ($doc->review_status !== 'pending_review' || ! $doc->updated_at) {
                return false;
            }

            if (! $doc->reviewed_at) {
                return true;
            }

            return $doc->updated_at->gt($doc->reviewed_at);
        })->count();

        return view('flux-admin.partials.rentals.documents-tab', [
            'booking'              => $booking,
            'documents'            => $documents,
            'checklist'            => $checklist,
            'missing'              => $missing,
            'lifecycleStatus'      => $lifecycle->lifecycleStatus($booking),
            'pendingInvoiceAmount' => $pendingInvoiceAmount,
            'pendingReviewCount'   => $pendingReviewCount,
            'newUploadCount'       => $newUploadCount,
            'customerWhatsappUrl'  => $lifecycle->customerWhatsappUrl($booking->customer),
            'documentsPhasePending'=> $lifecycle->documentsPhasePending($booking),
        ]);
    }
}
