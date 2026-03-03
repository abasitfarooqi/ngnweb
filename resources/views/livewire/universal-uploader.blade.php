@php
    $fileCount = is_array($files) ? count($files) : 0;
    $isDocumentMode = (bool) ($documentTypeId && $customerId);
@endphp
<div class="space-y-4" wire:key="universal-uploader-root">
    {{-- Storage & connections status --}}
    <div class="p-4 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Storage & connections</h3>
        <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
            <li><strong>Storage:</strong> {{ config('filesystems.default') }} (local)</li>
            <li><strong>Media disk:</strong> {{ config('media-library.disk_name') }} (DO Spaces)</li>
            <li><strong>DO Spaces:</strong> {{ config('filesystems.disks.spaces.bucket') ? 'configured' : 'not set' }}</li>
            <li><strong>Temp cleanup:</strong> runs every minute (files older than 10 mins deleted)</li>
            <li><strong>Media count:</strong> {{ \Spatie\MediaLibrary\MediaCollections\Models\Media::count() }} records</li>
        </ul>
    </div>

    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <form wire:submit.prevent="commit" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $isDocumentMode ? 'Upload Document' : 'Universal Uploader' }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $isDocumentMode ? 'Select a file. It will be stored on DO Spaces.' : 'Select files (staged temporarily). Click "Save to Media" to commit.' }}
                        <span class="block mt-1 text-xs">Files: {{ $fileCount }}</span>
                    </p>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="commit"
                        class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2 border border-transparent text-sm font-medium text-white bg-brand-red hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span wire:loading.remove wire:target="commit">{{ $isDocumentMode ? 'Upload to DO Spaces' : 'Save to Media (' . $fileCount . ')' }}</span>
                        <span wire:loading wire:target="commit" class="inline-flex items-center gap-2">
                            <span class="inline-block h-4 w-4 border-2 border-white border-t-transparent animate-spin" aria-hidden="true"></span>
                            Saving…
                        </span>
                    </button>
                    @if(!$isDocumentMode)
                    <button type="button" wire:click="testButton" class="mt-1 px-2 py-1 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        Test Livewire
                    </button>
                    @endif
                    <p wire:loading wire:target="commit" class="text-xs text-gray-500 dark:text-gray-400">Saving to media library…</p>
                    @error('files')
                        <p class="text-sm text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
                    @enderror
                    @if($commitError)
                        <p class="text-sm text-red-600 dark:text-red-400 max-w-md font-bold">{{ $commitError }}</p>
                    @endif
                </div>
            </div>

            <div
                x-data="{ over: false, uploadProgress: 0 }"
                @dragover.prevent="over = true"
                @dragleave.prevent="over = false"
                @drop.prevent="over = false"
                @upload-progress-update.window="uploadProgress = $event.detail.progress"
                :class="over ? 'border-brand-red bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/30'"
                class="border-2 border-dashed p-6 text-center transition-colors relative"
            >
                <div wire:loading wire:target="files" class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-white/95 dark:bg-gray-800/95 z-10">
                    <span class="inline-block h-10 w-10 border-4 border-brand-red border-t-transparent animate-spin" aria-hidden="true"></span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Uploading file(s) to temp storage…</span>
                    <div class="w-full max-w-xs px-4">
                        <div class="h-2 bg-gray-200 dark:bg-gray-600 overflow-hidden">
                            <div class="h-full bg-brand-red transition-all duration-200" :style="'width: ' + uploadProgress + '%'"></div>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="uploadProgress + '%'"></p>
                    </div>
                </div>
                <label class="block cursor-pointer">
                    <input
                        type="file"
                        wire:model.live="files"
                        {{ $multiple ? 'multiple' : '' }}
                        id="file-input-upload-{{ $this->getId() }}"
                        accept="{{ $accept ?: '.pdf,.jpg,.jpeg,.png,.gif,.webp,.txt,.doc,.docx' }}"
                        class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-gray-200 file:text-gray-800 dark:file:bg-gray-600 dark:file:text-gray-200 file:cursor-pointer"
                    />
                    <span class="mt-2 block text-xs text-gray-600 dark:text-gray-400">
                        Click or drag files here (max {{ number_format($maxSizeKb/1024) }}MB per file)
                    </span>
                </label>
                @if(!$isDocumentMode)
                <button type="button" wire:click="debugUpload" class="mt-2 text-xs px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    Debug
                </button>
                @endif
            </div>

            @error('files')
                <p class="mt-2 text-sm font-bold text-red-600 dark:text-red-400">Upload error: {{ $message }}</p>
            @enderror
            @error('files.*')
                <p class="mt-2 text-sm font-bold text-red-600 dark:text-red-400">File error: {{ $message }}</p>
            @enderror

            @if($isDocumentMode)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Number (optional)</label>
                <input type="text" wire:model="documentNumber" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid Until (optional)</label>
                <input type="date" wire:model="validUntil" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">
            </div>
            @endif

            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                <strong>Status:</strong>
                @if($fileCount > 0)
                    <span class="text-green-600 dark:text-green-400">{{ $fileCount }} file(s) ready to save</span>
                @else
                    <span class="text-yellow-600 dark:text-yellow-400">Waiting for file upload (select a file above)</span>
                @endif
            </div>

            @if($fileCount > 0 && is_array($files))
                <hr class="my-4 border-gray-200 dark:border-gray-700" />
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Staged files ({{ $fileCount }})</h3>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="commit"
                        class="text-xs px-3 py-1.5 text-white bg-brand-red hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-800 transition-colors disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="commit">Save All</span>
                        <span wire:loading wire:target="commit">Saving…</span>
                    </button>
                </div>
                <div class="space-y-2">
                    @foreach($files as $i => $f)
                        <div wire:key="staged-{{ $i }}" class="border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700/50 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $f->getClientOriginalName() }}
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $f->getMimeType() }} · {{ number_format($f->getSize() / 1024, 1) }} KB
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeTemp({{ $i }})"
                                    wire:loading.attr="disabled"
                                    wire:target="removeTemp"
                                    class="shrink-0 px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 border border-red-200 dark:border-red-800 transition-colors disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="removeTemp">Remove</span>
                                    <span wire:loading wire:target="removeTemp">…</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-sm text-yellow-800 dark:text-yellow-200">
                    <strong>No files staged yet.</strong> Select a file using the box above. Wait for it to upload to temp storage, then click "Save to Media".
                </div>
            @endif
        </form>
    </div>
</div>

@script
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ options }) => {
            const calls = options?.serverMemo?.calls ?? [];
            if (calls.some(c => c?.method === 'commit')) console.log('[Upload] Livewire commit request sent');
        });
    });

    window.addEventListener('livewire-upload-start', () => {
        window.dispatchEvent(new CustomEvent('upload-progress-update', { detail: { progress: 0 } }));
    });
    window.addEventListener('livewire-upload-progress', (e) => {
        window.dispatchEvent(new CustomEvent('upload-progress-update', { detail: { progress: e.detail.progress } }));
    });
    window.addEventListener('livewire-upload-finish', () => {
        window.dispatchEvent(new CustomEvent('upload-progress-update', { detail: { progress: 100 } }));
    });
    window.addEventListener('livewire-upload-error', (e) => {
        console.error('Upload error:', e.detail);
    });

    Livewire.on('upload-committed', () => {
        window.dispatchEvent(new CustomEvent('toast-show', { detail: { variant: 'success', message: 'Files were added to the media library.' } }));
        setTimeout(() => window.location.reload(), 2000);
    });
    Livewire.on('document-upload-committed', () => {
        console.log('document-upload-committed event received');
    });
</script>
@endscript
