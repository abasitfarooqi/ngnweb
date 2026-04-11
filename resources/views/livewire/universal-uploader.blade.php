@php
    $isDocumentMode = (bool) ($documentTypeId && $customerId);
@endphp
<div class="space-y-4" wire:key="universal-uploader-root">
    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $isDocumentMode ? 'Upload document' : 'Upload file' }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Choose file and click Upload. We handle sync and cleanup automatically.
                </p>
            </div>

            <div class="space-y-2">
                @if($isDocumentMode)
                    <input
                        type="file"
                        wire:model="file"
                        accept="{{ $accept ?: '.pdf,.jpg,.jpeg,.png,.gif,.webp,.txt,.doc,.docx' }}"
                        class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 dark:file:border-gray-600 file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-800 dark:file:text-gray-200 file:cursor-pointer"
                    />
                    <p wire:loading wire:target="file" class="text-xs text-gray-500 dark:text-gray-400">Preparing file…</p>
                    @error('file')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if($file)
                        <p class="text-xs text-green-600 dark:text-green-400">File ready: {{ $file->getClientOriginalName() }}</p>
                    @endif
                @else
                    <input
                        type="file"
                        wire:model="files"
                        {{ $multiple ? 'multiple' : '' }}
                        accept="{{ $accept ?: '.pdf,.jpg,.jpeg,.png,.gif,.webp,.txt,.doc,.docx' }}"
                        class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 dark:file:border-gray-600 file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-800 dark:file:text-gray-200 file:cursor-pointer"
                    />
                    <p wire:loading wire:target="files" class="text-xs text-gray-500 dark:text-gray-400">Preparing file…</p>
                    @error('files')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('files.*')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            @if($isDocumentMode)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document number (optional)</label>
                    <input type="text" wire:model="documentNumber" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid until (optional)</label>
                    <input type="date" wire:model="validUntil" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
            @endif

            @if($commitError)
                <p class="text-sm text-red-600 dark:text-red-400">{{ $commitError }}</p>
            @endif
            @if($commitSuccess)
                <p class="text-sm text-green-600 dark:text-green-400">{{ $commitSuccess }}</p>
            @endif

            <div class="flex justify-end">
                <button
                    type="button"
                    wire:click="commit"
                    wire:loading.attr="disabled"
                    wire:target="commit,file,files"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-red hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span wire:loading.remove wire:target="commit">Upload</span>
                    <span wire:loading wire:target="commit" class="inline-flex items-center gap-2">
                        <span class="inline-block h-4 w-4 border-2 border-white border-t-transparent animate-spin" aria-hidden="true"></span>
                        Uploading…
                    </span>
                </button>
            </div>
        </div>
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

    Livewire.on('upload-committed', () => {
        window.dispatchEvent(new CustomEvent('toast-show', { detail: { variant: 'success', message: 'File uploaded successfully.' } }));
    });

    Livewire.on('document-upload-committed', (payload) => {
        const name = payload?.fileName ? ` (${payload.fileName})` : '';
        window.alert(`Upload complete${name}.`);
    });

    Livewire.on('document-upload-failed', (payload) => {
        const message = payload?.message || 'Upload failed. Please try again.';
        window.alert(message);
    });
</script>
@endscript
