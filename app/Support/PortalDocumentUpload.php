<?php

namespace App\Support;

use App\Jobs\MoveCustomerDocumentToSpacesJob;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

final class PortalDocumentUpload
{
    /**
     * @return array{document: CustomerDocument, synced_now: bool, storage_target: string}
     */
    public function store(Customer $profile, int $documentTypeId, UploadedFile $file, ?string $validUntil = null): array
    {
        if (! $profile->canCustomerEditPortal()) {
            throw new RuntimeException('Document uploads are read-only until NGN authorises your account.');
        }

        if (! DocumentType::query()->whereKey($documentTypeId)->exists()) {
            throw new RuntimeException('Invalid document type.');
        }

        $lifecycle = app(RentalBookingLifecycle::class);
        $existing = CustomerDocument::query()
            ->where('customer_id', $profile->id)
            ->where('document_type_id', $documentTypeId)
            ->first();

        $existingStatus = $lifecycle->resolveCustomerDocumentStatus($existing);
        if (! $profile->canCustomerReplaceDocument($existingStatus)) {
            throw new RuntimeException('You cannot replace this document at the moment.');
        }

        $path = 'customer-documents/'.Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        CustomerDocumentStorage::put($path, $file->get());

        $oldPath = $existing?->file_path;

        $attributes = [
            'customer_id' => $profile->id,
            'document_type_id' => $documentTypeId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_format' => $file->getClientOriginalExtension(),
            'document_number' => '',
            'valid_until' => $validUntil ?: null,
        ];

        if (Schema::hasColumn('customer_documents', 'status')) {
            $attributes['status'] = 'pending_review';
        } elseif (Schema::hasColumn('customer_documents', 'is_verified')) {
            $attributes['is_verified'] = false;
        }

        if (Schema::hasColumn('customer_documents', 'rejection_reason')) {
            $attributes['rejection_reason'] = null;
        }

        if (Schema::hasColumn('customer_documents', 'booking_id')) {
            $attributes['booking_id'] = null;
        }

        $row = CustomerDocument::updateOrCreate([
            'customer_id' => $profile->id,
            'document_type_id' => $documentTypeId,
        ], $attributes);

        app(CustomerDocumentReviewNotifier::class)->logStaffUpload($row);
        app(CustomerDocumentReviewNotifier::class)->notifyStaffIfAllMandatorySubmitted($profile, null);

        if ($oldPath && $oldPath !== $path) {
            CustomerDocumentStorage::delete($oldPath);
        }

        MoveCustomerDocumentToSpacesJob::dispatch($row->id, $path)
            ->delay(now()->addMinutes(10));

        $syncedNow = CustomerDocumentStorage::moveToSpacesAndDeleteLocalIfSynced($path);
        $storageTarget = $syncedNow
            ? 'digitalocean-spaces (synced now)'
            : (CustomerDocumentStorage::spacesConfigured()
                ? 'site-storage (queued for digitalocean-spaces)'
                : 'site-storage');

        return [
            'document' => $row,
            'synced_now' => $syncedNow,
            'storage_target' => $storageTarget,
        ];
    }
}
