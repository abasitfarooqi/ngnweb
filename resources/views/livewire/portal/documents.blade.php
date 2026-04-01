<div wire:key="documents-page">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Documents</h1>
        <p class="text-sm text-gray-500 mt-1">Upload and manage rental, finance, and general documents from one place.</p>
    </div>

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

    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="switchTab('rental')"
                class="px-3 py-2 text-sm border-b-2 {{ $activeTab === 'rental' ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Rental Documents
            </button>
            <button type="button" wire:click="switchTab('finance')"
                class="px-3 py-2 text-sm border-b-2 {{ $activeTab === 'finance' ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Finance Documents
            </button>
            <button type="button" wire:click="switchTab('other')"
                class="px-3 py-2 text-sm border-b-2 {{ $activeTab === 'other' ? 'border-brand-red text-brand-red' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Other
            </button>
        </div>
    </div>

    @if($activeTab === 'rental')
        <div class="space-y-4">
            <flux:card class="p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Rental Required Documents</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Upload these documents before starting your rental agreement.</p>
                @if($missingRentalMandatory->isNotEmpty())
                    <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
                        <flux:callout.text>
                            {{ $missingRentalMandatory->count() }} mandatory rental document{{ $missingRentalMandatory->count() > 1 ? 's are' : ' is' }} still missing.
                        </flux:callout.text>
                    </flux:callout>
                @endif
                @if($rentalDocs->isEmpty())
                    <p class="text-sm text-gray-400">No rental document types defined yet. Please contact us.</p>
                @else
                    <div class="space-y-3">
                        @foreach($rentalDocs as $docType)
                            @php
                                $uploaded = $uploadedByType[$docType->id] ?? null;
                                $status   = $uploaded ? ($uploaded->status ?? 'pending_review') : 'missing';
                                $badgeColor = match($status) {
                                    'approved'       => 'green',
                                    'pending_review' => 'yellow',
                                    'rejected'       => 'red',
                                    default          => 'zinc',
                                };
                                $statusLabel = match($status) {
                                    'approved'       => 'Approved',
                                    'pending_review' => 'Under Review',
                                    'rejected'       => 'Rejected',
                                    default          => 'Missing',
                                };
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
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <flux:badge color="{{ $badgeColor }}" size="sm">{{ $statusLabel }}</flux:badge>
                                    @if($uploaded?->portal_file_url)
                                        <flux:button href="{{ $uploaded->portal_file_url }}" target="_blank" variant="outline" size="sm">View</flux:button>
                                    @endif
                                    <flux:button
                                        wire:key="upload-btn-rental-{{ $docType->id }}"
                                        wire:click="startUpload({{ $docType->id }})"
                                        variant="filled" size="sm"
                                        class="bg-brand-red text-white">
                                        {{ $uploaded ? 'Replace' : 'Upload' }}
                                    </flux:button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            <flux:card class="p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Uploaded Rental Files</h3>
                @if($rentalUploadedDocuments->isEmpty())
                    <p class="text-sm text-gray-500">No rental files uploaded yet.</p>
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
                                $status   = $uploaded ? ($uploaded->status ?? 'pending_review') : 'missing';
                                $badgeColor = match($status) {
                                    'approved'       => 'green',
                                    'pending_review' => 'yellow',
                                    'rejected'       => 'red',
                                    default          => 'zinc',
                                };
                                $statusLabel = match($status) {
                                    'approved'       => 'Approved',
                                    'pending_review' => 'Under Review',
                                    'rejected'       => 'Rejected',
                                    default          => 'Missing',
                                };
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
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <flux:badge color="{{ $badgeColor }}" size="sm">{{ $statusLabel }}</flux:badge>
                                    @if($uploaded?->portal_file_url)
                                        <flux:button href="{{ $uploaded->portal_file_url }}" target="_blank" variant="outline" size="sm">View</flux:button>
                                    @endif
                                    <flux:button
                                        wire:key="upload-btn-finance-{{ $docType->id }}"
                                        wire:click="startUpload({{ $docType->id }})"
                                        variant="filled" size="sm"
                                        class="bg-brand-red text-white">
                                        {{ $uploaded ? 'Replace' : 'Upload' }}
                                    </flux:button>
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

    @if($activeTab === 'other')
        <div class="space-y-4">
            <flux:card class="p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Other Document Types</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">General documents not specifically marked as rental or finance.</p>
                @if($otherDocs->isEmpty())
                    <p class="text-sm text-gray-500">No additional document types configured yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($otherDocs as $docType)
                            @php
                                $uploaded = $uploadedByType[$docType->id] ?? null;
                                $statusLabel = $uploaded ? 'Uploaded' : 'Missing';
                            @endphp
                            <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 dark:border-gray-700">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $docType->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $statusLabel }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($uploaded?->portal_file_url)
                                        <flux:button href="{{ $uploaded->portal_file_url }}" target="_blank" variant="outline" size="sm">View</flux:button>
                                    @endif
                                    <flux:button wire:click="startUpload({{ $docType->id }})" variant="filled" size="sm" class="bg-brand-red text-white">
                                        {{ $uploaded ? 'Replace' : 'Upload' }}
                                    </flux:button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">All Uploaded Files</h3>
                @if($uploadedDocuments->isEmpty())
                    <p class="text-sm text-gray-500">No documents uploaded yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($uploadedDocuments as $doc)
                            <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 dark:border-gray-700">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $doc->documentType?->name ?? 'Document' }}</p>
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
                        <livewire:universal-uploader
                            :document-type-id="$uploadingFor"
                            :customer-id="$customerId"
                            key="doc-uploader-{{ $uploadingFor }}-{{ $customerId }}"
                        />
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
