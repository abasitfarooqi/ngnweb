<div wire:key="documents-page">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Documents</h1>
        <p class="text-sm text-gray-500 mt-1">Upload and manage your documents for rentals, finance, and other services.</p>
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

    <flux:tabs wire:model="activeTab">
        <flux:tab name="rental">Rental Documents</flux:tab>
        <flux:tab name="finance">Finance Documents</flux:tab>
        <flux:tab name="other">Other</flux:tab>

        {{-- Rental tab panel --}}
        <flux:tab.panel name="rental">
            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Rental Requirements</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Upload these documents before starting your rental agreement.</p>
                @if($rentalDocs->isEmpty())
                    <p class="text-sm text-gray-400">No rental document types defined yet. Please contact us.</p>
                @else
                    <div class="space-y-3">
                        @foreach($rentalDocs as $docType)
                            @php
                                $uploaded = $uploadedDocs[$docType->id] ?? null;
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
                                    @if($uploaded && isset($uploaded->file_path))
                                        <flux:button href="{{ asset('storage/' . $uploaded->file_path) }}" target="_blank" variant="outline" size="sm">View</flux:button>
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
        </flux:tab.panel>

        {{-- Finance tab panel --}}
        <flux:tab.panel name="finance">
            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Finance Requirements</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Upload these documents as part of your finance application.</p>
                @if($financeDocs->isEmpty())
                    <p class="text-sm text-gray-400">No finance document types defined yet. Please contact us.</p>
                @else
                    <div class="space-y-3">
                        @foreach($financeDocs as $docType)
                            @php
                                $uploaded = $uploadedDocs[$docType->id] ?? null;
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
                                    @if($uploaded && isset($uploaded->file_path))
                                        <flux:button href="{{ asset('storage/' . $uploaded->file_path) }}" target="_blank" variant="outline" size="sm">View</flux:button>
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
        </flux:tab.panel>

        {{-- Other tab panel --}}
        <flux:tab.panel name="other">
            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Other Documents</h3>
                <p class="text-sm text-gray-500">Contact us to request additional document uploads.</p>
            </flux:card>
        </flux:tab.panel>
    </flux:tabs>

    {{-- Upload modal --}}
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
