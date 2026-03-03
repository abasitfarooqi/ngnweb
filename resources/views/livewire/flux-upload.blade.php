@php
    $fileCount = count($files);
    $isDocumentMode = (bool) ($documentTypeId && $customerId);
    $displayLabel = $label ?? ($isDocumentMode ? 'Upload document' : 'Upload files');
    $displayDesc = $description ?? ($isDocumentMode ? 'Select a file. It will be stored on DO Spaces.' : 'Drop files here or click to browse. Max ' . number_format($maxSizeKb / 1024) . 'MB per file.');
@endphp
<div wire:key="flux-upload-{{ $this->getId() }}" class="space-y-4">
    <form wire:submit="commit" class="space-y-4">
        <flux:field>
            <flux:label>{{ $displayLabel }}</flux:label>
            <flux:description>{{ $displayDesc }}</flux:description>

            @if($commitError)
                <flux:callout variant="danger" :text="$commitError" class="mt-2" />
            @endif

            <flux:file-upload wire:model.live="files" {{ $multiple ? 'multiple' : '' }} accept="{{ $accept ?: '.pdf,.jpg,.jpeg,.png,.gif,.webp,.txt,.doc,.docx' }}">
                <flux:file-upload.dropzone
                    heading="{{ $fileCount > 0 ? $fileCount . ' file(s) selected' : 'Click or drag files here' }}"
                    text="Max {{ number_format($maxSizeKb / 1024) }}MB per file"
                    with-progress
                />
            </flux:file-upload>

            @error('files')
                <flux:callout variant="danger" :text="$message" class="mt-2" />
            @enderror
            @error('files.*')
                <flux:callout variant="danger" :text="$message" class="mt-2" />
            @enderror

            @if($isDocumentMode)
                <div class="mt-4 space-y-4">
                    <flux:field>
                        <flux:label>Document number (optional)</flux:label>
                        <flux:input wire:model="documentNumber" placeholder="e.g. passport number" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Valid until (optional)</flux:label>
                        <flux:date-picker wire:model="validUntil" />
                    </flux:field>
                </div>
            @endif

            @if($fileCount > 0)
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $fileCount }} file(s) ready</span>
                        <flux:button type="submit" variant="primary" color="red" wire:loading.attr="disabled" wire:target="commit">
                            <span wire:loading.remove wire:target="commit">{{ $isDocumentMode ? 'Upload' : 'Save (' . $fileCount . ')' }}</span>
                            <span wire:loading wire:target="commit">Saving…</span>
                        </flux:button>
                    </div>
                    <div class="flex flex-col gap-2">
                        @foreach($files as $i => $f)
                            <flux:file-item
                                wire:key="file-{{ $i }}"
                                :heading="$f->getClientOriginalName()"
                                :size="$f->getSize()"
                            >
                                <x-slot:actions>
                                    <flux:button type="button" variant="ghost" size="sm" wire:click="removeTemp({{ $i }})" wire:loading.attr="disabled" wire:target="removeTemp">
                                        Remove
                                    </flux:button>
                                </x-slot:actions>
                            </flux:file-item>
                        @endforeach
                    </div>
                </div>
            @endif
        </flux:field>
    </form>
</div>
