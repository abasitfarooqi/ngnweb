<?php

namespace App\Support;

use App\Mail\CustomerDocumentRequest;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class CustomerDocumentReviewNotifier
{
    public function notifyCustomer(CustomerDocument $document, string $decision): void
    {
        if ($decision !== 'rejected') {
            return;
        }

        $document->loadMissing(['customer', 'documentType']);
        $customer = $document->customer;

        if (! $customer?->email) {
            return;
        }

        $docName = $document->documentType?->name ?? 'your document';
        $portalUrl = route('account.documents', array_filter([
            'tab' => 'rental',
            'booking_id' => $document->booking_id,
        ]));

        $uploadUrl = null;
        if ($document->booking_id && $document->customer_id) {
            $uploadUrl = app(DocumentUploadAccessGenerator::class)
                ->findActiveLink((int) $document->customer_id, (int) $document->booking_id);
        }

        $title = 'Please re-upload a document';
        $reason = trim((string) $document->rejection_reason);
        $body = "We need you to upload a new copy of your {$docName}."
            .($reason !== '' ? "\n\nReason: {$reason}" : '')
            ."\n\nUse the upload link below or sign in to your account and replace the file.";
        $actionUrl = $uploadUrl ?: $portalUrl;
        $actionLabel = $uploadUrl ? 'Upload document' : 'Open my documents';

        try {
            Mail::to([
                $customer->email,
                'customerservice@neguinhomotors.co.uk',
            ])->send(new CustomerDocumentRequest([
                'title' => $title,
                'body' => $body,
                'url' => $actionUrl,
                'actionLabel' => $actionLabel,
                'customer_name' => trim($customer->first_name.' '.$customer->last_name),
            ]));
        } catch (Exception $e) {
            Log::error('Customer document review email failed: '.$e->getMessage(), [
                'document_id' => $document->id,
                'decision' => $decision,
            ]);
        }
    }

    public function notifyStaffIfAllMandatorySubmitted(Customer $customer, ?int $bookingId = null): void
    {
        $mandatoryTypes = DocumentType::query()
            ->forCustomerUpload()
            ->when(Schema::hasColumn('document_types', 'is_mandatory'), fn ($q) => $q->where('is_mandatory', true))
            ->orderBy('sort_order')
            ->get();

        if ($mandatoryTypes->isEmpty()) {
            return;
        }

        $uploadedByType = CustomerDocument::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('id')
            ->get()
            ->unique('document_type_id')
            ->keyBy('document_type_id');

        $lifecycle = app(RentalBookingLifecycle::class);
        $allUploaded = $mandatoryTypes->every(function (DocumentType $type) use ($uploadedByType, $lifecycle): bool {
            if (in_array((string) $type->code, DocumentType::STAFF_ISSUED_CODES, true)
                || in_array((string) $type->name, DocumentType::STAFF_ISSUED_NAMES, true)) {
                return true;
            }

            $doc = $uploadedByType->get($type->id);
            $status = $lifecycle->resolveCustomerDocumentStatus($doc);

            // Staff-notify when nothing still needing upload (uploaded/pending/approved is enough).
            return ! $lifecycle->documentNeedsUpload($status);
        });

        if (! $allUploaded) {
            $this->clearStaffMandatorySubmittedFlag($customer->id, $bookingId);

            return;
        }

        $cacheKey = $this->staffMandatorySubmittedCacheKey($customer->id, $bookingId);
        if (Cache::get($cacheKey)) {
            return;
        }

        $customerName = trim($customer->first_name.' '.$customer->last_name) ?: ($customer->email ?? 'Customer');
        $reviewUrl = $bookingId
            ? route('flux-admin.rentals.show', ['booking' => $bookingId, 'activeTab' => 'documents'])
            : route('flux-admin.customer-documents.index', ['filters' => ['status' => 'pending_review']]);

        $body = "{$customerName} has uploaded all required documents."
            .($bookingId ? " Rental booking #{$bookingId}." : '')
            ."\n\nPlease review and validate them in Flux Admin.";

        try {
            Mail::to('customerservice@neguinhomotors.co.uk')->send(new CustomerDocumentRequest([
                'title' => 'All required documents submitted',
                'body' => $body,
                'url' => $reviewUrl,
                'actionLabel' => 'Review documents',
                'customer_name' => $customerName,
            ]));
            Cache::forever($cacheKey, now()->toIso8601String());
        } catch (Exception $e) {
            Log::error('Staff mandatory documents email failed: '.$e->getMessage(), [
                'customer_id' => $customer->id,
                'booking_id' => $bookingId,
            ]);
        }
    }

    public function clearStaffMandatorySubmittedFlag(int $customerId, ?int $bookingId = null): void
    {
        Cache::forget($this->staffMandatorySubmittedCacheKey($customerId, $bookingId));
    }

    protected function staffMandatorySubmittedCacheKey(int $customerId, ?int $bookingId): string
    {
        return 'staff_mandatory_docs_ready:'.$customerId.':'.($bookingId ?? 'general');
    }

    public function logStaffUpload(CustomerDocument $document): void
    {
        $document->loadMissing(['documentType', 'customer']);

        Log::info('customer_document_uploaded_for_review', [
            'document_id' => $document->id,
            'customer_id' => $document->customer_id,
            'booking_id' => $document->booking_id,
            'document_type' => $document->documentType?->name,
            'file_name' => $document->file_name,
            'customer_email' => $document->customer?->email,
        ]);
    }
}
