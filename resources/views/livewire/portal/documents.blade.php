<div wire:key="documents-page">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Documents</h1>
        <p class="text-sm text-gray-500 mt-1">Upload and manage rental and general documents, and finance documents, from one place.</p>
    </div>

    @if($profile && ! $profile->canCustomerEditPortal())
        <flux:callout variant="warning" icon="lock-closed" class="mb-5">
            <flux:callout.text>
                Your account is read-only until NGN authorises editing. You can view documents below but cannot upload or replace them yet.
            </flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif
    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-5">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif
    @php
        $reuploadRequired = $uploadedByType->filter(function ($doc) use ($documentLifecycle) {
            return $documentLifecycle->resolveCustomerDocumentStatus($doc) === 'rejected';
        });
    @endphp
    @if($reuploadRequired->isNotEmpty())
        <flux:callout variant="danger" icon="exclamation-triangle" class="mb-5">
            <flux:callout.text>
                <strong>Action needed:</strong> we asked you to re-upload
                {{ $reuploadRequired->count() === 1 ? $reuploadRequired->first()->documentType?->name : $reuploadRequired->count().' documents' }}.
                Use <strong>Replace</strong> on the items marked <strong>Re-upload requested</strong> below.
            </flux:callout.text>
        </flux:callout>
    @endif
    @if($lastUploadReceipt)
        <flux:callout variant="success" icon="check-badge" class="mb-5">
            <flux:callout.text>
                Upload confirmed: {{ $lastUploadReceipt['document_type'] }} saved as {{ $lastUploadReceipt['file_name'] }}
                at {{ \Carbon\Carbon::parse($lastUploadReceipt['uploaded_at'])->format('d M Y H:i:s') }}
                via {{ $lastUploadReceipt['storage_target'] }}.
            </flux:callout.text>
        </flux:callout>
    @endif

    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="switchTab('rental')"
                class="px-3 py-2 text-sm border-b-2 {{ $activeTab === 'rental' ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Rental and general
            </button>
            <button type="button" wire:click="switchTab('finance')"
                class="px-3 py-2 text-sm border-b-2 {{ $activeTab === 'finance' ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Finance Documents
            </button>
        </div>
    </div>

    @if($activeTab === 'rental')
        <div class="space-y-4">
            @if($rentalBookingId)
                <flux:callout variant="info" icon="information-circle" class="mb-4">
                    <flux:callout.text>Documents are saved against your customer profile and reused across rentals. Optional expiry dates help us ask again only when a document has run out.</flux:callout.text>
                </flux:callout>
            @endif
            <flux:card class="p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Rental and general documents</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Includes rental requirements and any general document types not classed as finance. Re-upload is needed only when a document is missing, rejected, or expired.</p>
                @if($missingRentalMandatory->isNotEmpty())
                    <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
                        <flux:callout.text>
                            {{ $missingRentalMandatory->count() }} rental document{{ $missingRentalMandatory->count() > 1 ? 's need' : ' needs' }} uploading (missing, expired, or re-upload requested).
                        </flux:callout.text>
                    </flux:callout>
                @endif
                @if($rentalAndGeneralDocs->isEmpty())
                    <p class="text-sm text-gray-400">No document types defined yet. Please contact us.</p>
                @else
                    <div class="space-y-3">
                        @foreach($rentalAndGeneralDocs as $docType)
                            @php
                                $uploaded = $uploadedByType[$docType->id] ?? null;
                                $status = $documentLifecycle->resolveCustomerDocumentStatus($uploaded);
                                $badgeColor = match($status) {
                                    'approved' => 'green',
                                    'pending_review' => 'yellow',
                                    'rejected', 'expired' => 'red',
                                    default => 'zinc',
                                };
                                $statusLabel = $documentLifecycle->documentStatusLabel($status);
                                $canReplace = $profile && $profile->canCustomerReplaceDocument($status);
                                $canDelete = $profile && $uploaded && $profile->canCustomerDeleteDocument($status);
                            @endphp
                            <div class="flex items-start sm:items-center justify-between gap-3 p-4 border border-gray-200 dark:border-gray-700 flex-wrap sm:flex-nowrap">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $docType->name }}
                                        @if(isset($docType->is_mandatory) && !$docType->is_mandatory)
                                            <span class="text-xs text-gray-400 font-normal ml-1">(Optional)</span>
                                        @endif
                                    </p>
                                    @if($docType->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $docType->description }}</p>
                                    @endif
                                    @if($uploaded)
                                        <p class="text-xs text-gray-400 mt-1">
                                            Uploaded: {{ $uploaded->created_at->format('d M Y') }}
                                            @if($uploaded->valid_until) · Expires: {{ \Carbon\Carbon::parse($uploaded->valid_until)->format('d M Y') }} @endif
                                        </p>
                                        @if($status === 'rejected' && $uploaded->rejection_reason)
                                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">Reason: {{ $uploaded->rejection_reason }}</p>
                                        @endif
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <flux:badge color="{{ $badgeColor }}" size="sm">{{ $statusLabel }}</flux:badge>
                                    @if($uploaded?->portal_file_url)
                                        <flux:button href="{{ $uploaded->portal_file_url }}" target="_blank" variant="outline" size="sm">View</flux:button>
                                    @endif
                                    @if($canReplace || ! $uploaded)
                                        <flux:button
                                            wire:key="upload-btn-rental-{{ $docType->id }}"
                                            wire:click="startUpload({{ $docType->id }})"
                                            variant="filled" size="sm"
                                            class="bg-brand-red text-white">
                                            {{ $uploaded ? 'Replace' : 'Upload' }}
                                        </flux:button>
                                    @elseif($status === 'pending_review')
                                        <span class="text-xs text-amber-600 dark:text-amber-400">Awaiting review</span>
                                    @elseif($status === 'approved')
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Locked</span>
                                    @endif
                                    @if($canDelete)
                                        <flux:button
                                            wire:click="deleteDocument({{ $docType->id }})"
                                            wire:confirm="Remove this document?"
                                            variant="ghost" size="sm"
                                            class="text-red-600">
                                            Delete
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            <flux:card class="p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Uploaded rental and general files</h3>
                @if($rentalUploadedDocuments->isEmpty())
                    <p class="text-sm text-gray-500">No files uploaded yet in this section.</p>
                @else
                    <div class="space-y-2">
                        @foreach($rentalUploadedDocuments as $doc)
                            <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 dark:border-gray-700">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $doc->documentType?->name ?? 'Rental document' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $doc->file_name ?: 'Unnamed file' }} · {{ optional($doc->created_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                                @if($doc->portal_file_url)
                                    <flux:button href="{{ $doc->portal_file_url }}" target="_blank" variant="outline" size="sm">Open</flux:button>
                                @else
                                    <span class="text-xs text-gray-400">Stored privately</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Signed Rental Agreements</h3>
                @if($rentalAgreements->isEmpty())
                    <p class="text-sm text-gray-500">No signed rental agreement found yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($rentalAgreements as $agreement)
                            <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 dark:border-gray-700">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        Rental Agreement @if($agreement->booking_id) #{{ $agreement->booking_id }} @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $agreement->file_name ?: 'Agreement file' }} · {{ optional($agreement->created_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                                @if($agreement->portal_file_url)
                                    <flux:button href="{{ $agreement->portal_file_url }}" target="_blank" variant="outline" size="sm">Open</flux:button>
                                @else
                                    <span class="text-xs text-gray-400">Stored privately</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>
    @endif

    @if($activeTab === 'finance')
        <div class="space-y-4">
            <flux:card class="p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Finance Required Documents</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Upload these documents as part of your finance application.</p>
                @if($missingFinanceMandatory->isNotEmpty())
                    <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
                        <flux:callout.text>
                            {{ $missingFinanceMandatory->count() }} mandatory finance document{{ $missingFinanceMandatory->count() > 1 ? 's are' : ' is' }} still missing.
                        </flux:callout.text>
                    </flux:callout>
                @endif
                @if($financeDocs->isEmpty())
                    <p class="text-sm text-gray-400">No finance document types defined yet. Please contact us.</p>
                @else
                    <div class="space-y-3">
                        @foreach($financeDocs as $docType)
                            @php
                                $uploaded = $uploadedByType[$docType->id] ?? null;
                                $status = $documentLifecycle->resolveCustomerDocumentStatus($uploaded);
                                $badgeColor = match($status) {
                                    'approved' => 'green',
                                    'pending_review' => 'yellow',
                                    'rejected', 'expired' => 'red',
                                    default => 'zinc',
                                };
                                $statusLabel = $documentLifecycle->documentStatusLabel($status);
                                $canReplace = $profile && $profile->canCustomerReplaceDocument($status);
                                $canDelete = $profile && $uploaded && $profile->canCustomerDeleteDocument($status);
                            @endphp
                            <div class="flex items-start sm:items-center justify-between gap-3 p-4 border border-gray-200 dark:border-gray-700 flex-wrap sm:flex-nowrap">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $docType->name }}
                                        @if(isset($docType->is_mandatory) && !$docType->is_mandatory)
                                            <span class="text-xs text-gray-400 font-normal ml-1">(Optional)</span>
                                        @endif
                                    </p>
                                    @if($docType->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $docType->description }}</p>
                                    @endif
                                    @if($uploaded)
                                        <p class="text-xs text-gray-400 mt-1">
                                            Uploaded: {{ $uploaded->created_at->format('d M Y') }}
                                            @if($uploaded->valid_until) · Expires: {{ \Carbon\Carbon::parse($uploaded->valid_until)->format('d M Y') }} @endif
                                        </p>
                                        @if($status === 'rejected' && $uploaded->rejection_reason)
                                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">Reason: {{ $uploaded->rejection_reason }}</p>
                                        @endif
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <flux:badge color="{{ $badgeColor }}" size="sm">{{ $statusLabel }}</flux:badge>
                                    @if($uploaded?->portal_file_url)
                                        <flux:button href="{{ $uploaded->portal_file_url }}" target="_blank" variant="outline" size="sm">View</flux:button>
                                    @endif
                                    @if($canReplace || ! $uploaded)
                                        <flux:button
                                            wire:key="upload-btn-finance-{{ $docType->id }}"
                                            wire:click="startUpload({{ $docType->id }})"
                                            variant="filled" size="sm"
                                            class="bg-brand-red text-white">
                                            {{ $uploaded ? 'Replace' : 'Upload' }}
                                        </flux:button>
                                    @elseif($status === 'pending_review')
                                        <span class="text-xs text-amber-600 dark:text-amber-400">Awaiting review</span>
                                    @elseif($status === 'approved')
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Locked</span>
                                    @endif
                                    @if($canDelete)
                                        <flux:button
                                            wire:click="deleteDocument({{ $docType->id }})"
                                            wire:confirm="Remove this document?"
                                            variant="ghost" size="sm"
                                            class="text-red-600">
                                            Delete
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            <flux:card class="p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Uploaded Finance Files</h3>
                @if($financeUploadedDocuments->isEmpty())
                    <p class="text-sm text-gray-500">No finance files uploaded yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($financeUploadedDocuments as $doc)
                            <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 dark:border-gray-700">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $doc->documentType?->name ?? 'Finance document' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $doc->file_name ?: 'Unnamed file' }} · {{ optional($doc->created_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                                @if($doc->portal_file_url)
                                    <flux:button href="{{ $doc->portal_file_url }}" target="_blank" variant="outline" size="sm">Open</flux:button>
                                @else
                                    <span class="text-xs text-gray-400">Stored privately</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Signed Finance Contracts</h3>
                @if($financeContracts->isEmpty())
                    <p class="text-sm text-gray-500">No signed finance contract found yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($financeContracts as $contract)
                            <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 dark:border-gray-700">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        Finance Contract @if($contract->application_id) #{{ $contract->application_id }} @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $contract->file_name ?: 'Contract file' }} · {{ optional($contract->created_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                                @if($contract->portal_file_url)
                                    <flux:button href="{{ $contract->portal_file_url }}" target="_blank" variant="outline" size="sm">Open</flux:button>
                                @else
                                    <span class="text-xs text-gray-400">Stored privately</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>
    @endif

    @if($uploadingFor)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:key="doc-upload-modal">
            <div class="bg-white dark:bg-gray-800 w-full max-w-lg shadow-2xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Upload Document</h3>
                    <button wire:click="cancelUpload" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6">
                    @if($customerId)
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select file</label>
                                <input
                                    type="file"
                                    wire:model="file"
                                    name="file"
                                    class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 dark:file:border-gray-600 file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-800 dark:file:text-gray-200 file:cursor-pointer"
                                />
                                <p wire:loading wire:target="file" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Preparing file…</p>
                                @error('file')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid until (optional)</label>
                                <input type="date" wire:model="valid_until" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="flex justify-end gap-2">
                                <flux:button wire:click="cancelUpload" variant="outline">Cancel</flux:button>
                                <flux:button wire:click="submitDocumentUpload" wire:loading.attr="disabled" wire:target="submitDocumentUpload,file" variant="filled" class="bg-brand-red text-white">
                                    <span wire:loading.remove wire:target="submitDocumentUpload">Upload</span>
                                    <span wire:loading wire:target="submitDocumentUpload">Uploading…</span>
                                </flux:button>
                            </div>
                        </div>
                    @else
                        <flux:callout variant="warning" icon="exclamation-triangle">
                            <flux:callout.text>Please complete your profile before uploading documents.</flux:callout.text>
                        </flux:callout>
                        <div class="mt-4">
                            <flux:button wire:click="cancelUpload" variant="outline">Close</flux:button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Livewire.on('portal-document-upload-popup', (payload) => {
        const message = payload?.message || 'Upload completed.';
        window.alert(message);
    });
</script>
@endscript
