<div wire:key="documents-page">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Documents</h1>

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

    {{-- Flux Pro tabs --}}
    <flux:tabs wire:model="activeTab">
        <flux:tab name="rental">Rental Documents</flux:tab>
        <flux:tab name="finance">Finance Documents</flux:tab>
        <flux:tab name="other">Other</flux:tab>

        {{-- Rental tab panel --}}
        <flux:tab.panel name="rental">
            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Rental Requirements</h3>
                <div class="space-y-3">
                    @foreach($rentalDocs as $docType)
                        @php
                            $uploaded    = $uploadedDocs[$docType->id] ?? null;
                            $statusLabel = $uploaded ? $uploaded->statusLabel : ['label' => 'Missing', 'color' => 'gray'];
                            $colorMap = ['green'=>'green','yellow'=>'yellow','red'=>'red','gray'=>'zinc'];
                            $badgeColor = $colorMap[$statusLabel['color']] ?? 'zinc';
                        @endphp
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex-1 min-w-0 mr-4">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $docType->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $docType->description }}</p>
                                @if($uploaded)
                                    <p class="text-xs text-gray-400 mt-1">
                                        Uploaded: {{ $uploaded->created_at->format('d M Y') }}
                                        @if($uploaded->valid_until) · Expires: {{ $uploaded->valid_until->format('d M Y') }} @endif
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <flux:badge color="{{ $badgeColor }}" class="text-xs">{{ $statusLabel['label'] }}</flux:badge>
                                @if(!$uploaded)
                                    <flux:button
                                        wire:key="upload-btn-rental-{{ $docType->id }}"
                                        wire:click="startUpload({{ $docType->id }})"
                                        wire:loading.attr="disabled"
                                        variant="filled"
                                        size="sm"
                                        class="bg-brand-red text-white"
                                    >
                                        Upload
                                    </flux:button>
                                @else
                                    <flux:button href="{{ $uploaded->file_url }}" target="_blank" variant="outline" size="sm">View</flux:button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        </flux:tab.panel>

        {{-- Finance tab panel --}}
        <flux:tab.panel name="finance">
            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Finance Requirements</h3>
                <div class="space-y-3">
                    @foreach($financeDocs as $docType)
                        @php
                            $uploaded    = $uploadedDocs[$docType->id] ?? null;
                            $statusLabel = $uploaded ? $uploaded->statusLabel : ['label' => 'Missing', 'color' => 'gray'];
                            $colorMap = ['green'=>'green','yellow'=>'yellow','red'=>'red','gray'=>'zinc'];
                            $badgeColor = $colorMap[$statusLabel['color']] ?? 'zinc';
                        @endphp
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex-1 min-w-0 mr-4">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $docType->name }}
                                    @if(!$docType->is_mandatory) <span class="text-xs text-gray-500 font-normal">(Optional)</span> @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $docType->description }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <flux:badge color="{{ $badgeColor }}" class="text-xs">{{ $statusLabel['label'] }}</flux:badge>
                                @if(!$uploaded)
                                    <flux:button
                                        wire:key="upload-btn-finance-{{ $docType->id }}"
                                        wire:click="startUpload({{ $docType->id }})"
                                        variant="filled" size="sm" class="bg-brand-red text-white"
                                    >Upload</flux:button>
                                @else
                                    <flux:button href="{{ $uploaded->file_url }}" target="_blank" variant="outline" size="sm">View</flux:button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        </flux:tab.panel>

        {{-- Other tab panel --}}
        <flux:tab.panel name="other">
            <flux:card class="p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Other Documents</h3>
                <p class="text-sm text-gray-500">Additional documents will appear here once uploaded.</p>
            </flux:card>
        </flux:tab.panel>
    </flux:tabs>

    {{-- Upload Modal (Universal Uploader in document mode, keyed by legacy customers.id) --}}
    @if($uploadingFor && $customerId)
        <flux:modal name="doc-upload" :show="true" class="max-w-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="base">Upload Document</flux:heading>
                    <flux:button wire:click="cancelUpload" variant="ghost" size="sm" icon="x-mark" />
                </div>
                <livewire:universal-uploader
                    :document-type-id="$uploadingFor"
                    :customer-id="$customerId"
                    :document-number="$document_number"
                    :valid-until="$valid_until"
                    key="doc-uploader-{{ $uploadingFor }}-{{ $customerId }}"
                />
            </div>
        </flux:modal>
    @endif
</div>
