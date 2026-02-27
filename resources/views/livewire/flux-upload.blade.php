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

            {{-- Dropzone --}}
            <div
                x-data="{ over: false, uploadProgress: 0 }"
                @dragover.prevent="over = true"
                @dragleave.prevent="over = false"
                @drop.prevent="over = false"
                @upload-progress-update.window="uploadProgress = $event.detail.progress"
                :class="over ? 'border-zinc-400 bg-zinc-100 dark:bg-white/15 dark:border-zinc-500' : 'border-zinc-200 dark:border-zinc-600 bg-zinc-50/50 dark:bg-zinc-800/30'"
                class="mt-3 border-2 border-dashed p-6 text-center transition-colors relative"
            >
                <div wire:loading wire:target="files" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white/95 dark:bg-zinc-900/95 z-10">
                    <flux:icon icon="loading" variant="mini" class="text-zinc-500 dark:text-zinc-400" />
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Uploading…</span>
                    <div class="w-full max-w-xs px-4">
                        <div class="h-1.5 bg-zinc-200 dark:bg-zinc-600 overflow-hidden">
                            <div class="h-full bg-zinc-600 dark:bg-zinc-400 transition-all duration-200" :style="'width: ' + uploadProgress + '%'"></div>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1" x-text="uploadProgress + '%'"></p>
                    </div>
                </div>
                <label class="block cursor-pointer">
                    <input
                        type="file"
                        wire:model.live="files"
                        {{ $multiple ? 'multiple' : '' }}
                        id="flux-upload-input-{{ $this->getId() }}"
                        accept="{{ $accept ?: '.pdf,.jpg,.jpeg,.png,.gif,.webp,.txt,.doc,.docx' }}"
                        class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-zinc-200 file:text-zinc-800 dark:file:bg-zinc-600 dark:file:text-zinc-200 file:cursor-pointer"
                    />
                    <span class="mt-2 block text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $fileCount > 0 ? $fileCount . ' file(s) selected' : 'Click or drag files here' }}
                    </span>
                </label>
            </div>

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
                        <flux:input type="date" wire:model="validUntil" />
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
                            <div wire:key="file-{{ $i }}" class="flex items-center justify-between gap-3 p-3 border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800/50">
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $f->getClientOriginalName() }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($f->getSize() / 1024, 1) }} KB</div>
                                </div>
                                <flux:button type="button" variant="ghost" size="sm" wire:click="removeTemp({{ $i }})" wire:loading.attr="disabled" wire:target="removeTemp">
                                    Remove
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </flux:field>
    </form>
</div>

@script
<script>
    window.addEventListener('livewire-upload-start', () => {
        window.dispatchEvent(new CustomEvent('upload-progress-update', { detail: { progress: 0 } }));
    });
    window.addEventListener('livewire-upload-progress', (e) => {
        window.dispatchEvent(new CustomEvent('upload-progress-update', { detail: { progress: e.detail.progress } }));
    });
    window.addEventListener('livewire-upload-finish', () => {
        window.dispatchEvent(new CustomEvent('upload-progress-update', { detail: { progress: 100 } }));
    });
</script>
@endscript
