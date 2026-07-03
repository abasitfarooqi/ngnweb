<?php

namespace App\Support;

use App\Mail\CustomerDocumentRequest;
use App\Models\Customer;
use App\Models\CustomerDocument;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerDocumentReviewNotifier
{
    public function notifyCustomer(CustomerDocument $document, string $decision): void
    {
        if (! in_array($decision, ['approved', 'rejected'], true)) {
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

        if ($decision === 'approved') {
            $title = 'Document approved';
            $body = "Good news — we have approved your {$docName}.\n\nYou can view your documents any time in your account.";
            $actionUrl = $portalUrl;
            $actionLabel = 'View my documents';
        } else {
            $title = 'Please re-upload a document';
            $reason = trim((string) $document->rejection_reason);
            $body = "We need you to upload a new copy of your {$docName}."
                .($reason !== '' ? "\n\nReason: {$reason}" : '')
                ."\n\nUse the upload link below or sign in to your account and replace the file.";
            $actionUrl = $uploadUrl ?: $portalUrl;
            $actionLabel = $uploadUrl ? 'Upload document' : 'Open my documents';
        }

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
