<div class="space-y-4">
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <flux:heading size="lg">DO Spaces vault</flux:heading>
                <p class="text-sm text-zinc-500 mt-1">
                    Private bucket browser — not linked in the menu.
                    @if($bucket)
                        <span class="font-mono text-xs">{{ $bucket }}</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($configured && $listing['path'] !== '')
                    <flux:button size="sm" variant="outline" wire:click="goUp" icon="arrow-up" class="!rounded-none">Up</flux:button>
                @endif
                <div class="min-w-[14rem]">
                    <flux:input wire:model.live.debounce.300ms="filter" placeholder="Filter in this folder…" class="!rounded-none" />
                </div>
            </div>
        </div>

        @if(! $configured)
            <div class="mt-4 border border-amber-300 bg-amber-50 text-amber-900 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-200 p-3 text-sm">
                DO Spaces is not configured. Set <span class="font-mono">DO_SPACES_KEY</span>, <span class="font-mono">DO_SPACES_SECRET</span> and <span class="font-mono">DO_SPACES_BUCKET</span>.
            </div>
        @elseif($error)
            <div class="mt-4 border border-red-300 bg-red-50 text-red-900 dark:border-red-700 dark:bg-red-950/40 dark:text-red-200 p-3 text-sm">{{ $error }}</div>
        @else
            <nav class="mt-4 flex flex-wrap items-center gap-1 text-sm" aria-label="Breadcrumb">
                @foreach($listing['breadcrumbs'] as $crumb)
                    @if(! $loop->first)
                        <span class="text-zinc-400">/</span>
                    @endif
                    <button
                        type="button"
                        wire:click="goTo('{{ $crumb['path'] }}')"
                        @class([
                            'font-mono px-1 py-0.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition',
                            'text-zinc-900 dark:text-white font-medium' => $loop->last,
                            'text-zinc-500' => ! $loop->last,
                        ])
                    >{{ $crumb['label'] }}</button>
                @endforeach
            </nav>
        @endif
    </div>

    @if($configured && ! $error)
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
            <div class="xl:col-span-7 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Name</flux:table.column>
                        <flux:table.column>Type</flux:table.column>
                        <flux:table.column>Size</flux:table.column>
                        <flux:table.column>Modified</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($listing['folders'] as $folder)
                            <flux:table.row wire:key="folder-{{ $folder['path'] }}" class="cursor-pointer" wire:click="enterFolder('{{ $folder['name'] }}')">
                                <flux:table.cell>
                                    <span class="inline-flex items-center gap-2 font-medium text-zinc-900 dark:text-white">
                                        <span class="text-amber-500 text-xs uppercase tracking-wide">dir</span>
                                        {{ $folder['name'] }}
                                    </span>
                                </flux:table.cell>
                                <flux:table.cell class="text-zinc-500">Folder</flux:table.cell>
                                <flux:table.cell class="text-zinc-500">—</flux:table.cell>
                                <flux:table.cell class="text-zinc-500">—</flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="xs" variant="ghost" wire:click.stop="enterFolder('{{ $folder['name'] }}')" class="!rounded-none">Open</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                        @endforelse

                        @forelse($listing['files'] as $file)
                            <flux:table.row wire:key="file-{{ $file['path'] }}" @class(['bg-zinc-50 dark:bg-zinc-800/50' => $previewPath === $file['path']])>
                                <flux:table.cell>
                                    <span class="inline-flex items-center gap-2 text-zinc-900 dark:text-white">
                                        <span class="text-zinc-400 text-xs uppercase tracking-wide">file</span>
                                        <span class="font-mono text-xs">{{ $file['name'] }}</span>
                                    </span>
                                </flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-500">{{ $file['mime'] ?: 'file' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ \App\Support\SpacesVault::formatBytes($file['size']) }}</flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-500 whitespace-nowrap">
                                    {{ $file['modified'] ? \Carbon\Carbon::createFromTimestamp($file['modified'])->format('d M Y H:i') : '—' }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-1">
                                        @if($file['previewable'])
                                            <flux:button size="xs" variant="ghost" wire:click="openPreview('{{ $file['path'] }}')" class="!rounded-none">View</flux:button>
                                        @endif
                                        <a href="{{ route('flux-admin.spaces-vault.stream', ['path' => $file['path'], 'disposition' => 'attachment']) }}">
                                            <flux:button size="xs" variant="outline" class="!rounded-none">Download</flux:button>
                                        </a>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            @if($listing['folders'] === [])
                                <flux:table.row>
                                    <flux:table.cell colspan="5" class="text-center py-10 text-zinc-500">This folder is empty.</flux:table.cell>
                                </flux:table.row>
                            @endif
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="xl:col-span-5 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 min-h-[24rem]">
                <div class="border-b border-zinc-200 dark:border-zinc-700 px-4 py-3 flex items-center justify-between">
                    <flux:heading size="sm">Preview</flux:heading>
                    @if($previewFile)
                        <flux:button size="xs" variant="ghost" wire:click="closePreview" class="!rounded-none">Close</flux:button>
                    @endif
                </div>

                <div class="p-4">
                    @if(! $previewFile)
                        <p class="text-sm text-zinc-500">Select a file and choose View, or download directly from the list.</p>
                    @else
                        <div class="mb-3">
                            <p class="font-mono text-xs text-zinc-900 dark:text-white break-all">{{ $previewFile['path'] }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if($previewFile['previewable'] ?? false)
                                    <a href="{{ route('flux-admin.spaces-vault.stream', ['path' => $previewFile['path'], 'disposition' => 'inline']) }}" target="_blank" rel="noopener">
                                        <flux:button size="xs" variant="outline" class="!rounded-none">Open in tab</flux:button>
                                    </a>
                                @endif
                                <a href="{{ route('flux-admin.spaces-vault.stream', ['path' => $previewFile['path'], 'disposition' => 'attachment']) }}">
                                    <flux:button size="xs" variant="primary" class="!rounded-none">Download</flux:button>
                                </a>
                            </div>
                        </div>

                        @if($previewFile['previewable'] ?? false)
                            @php
                                $inlineUrl = route('flux-admin.spaces-vault.stream', ['path' => $previewFile['path'], 'disposition' => 'inline']);
                                $mime = strtolower((string) ($previewFile['mime'] ?? ''));
                                $isImage = str_starts_with($mime, 'image/');
                                $isPdf = $mime === 'application/pdf' || str_ends_with(strtolower($previewFile['name']), '.pdf');
                                $isText = str_starts_with($mime, 'text/') || in_array(strtolower(pathinfo($previewFile['name'], PATHINFO_EXTENSION)), ['txt','csv','json','xml','log','md'], true);
                            @endphp

                            @if($isImage)
                                <img src="{{ $inlineUrl }}" alt="{{ $previewFile['name'] }}" class="max-w-full border border-zinc-200 dark:border-zinc-700" />
                            @elseif($isPdf)
                                <iframe src="{{ $inlineUrl }}" title="{{ $previewFile['name'] }}" class="w-full h-[32rem] border border-zinc-200 dark:border-zinc-700 bg-white"></iframe>
                            @elseif($isText)
                                <iframe src="{{ $inlineUrl }}" title="{{ $previewFile['name'] }}" class="w-full h-[32rem] border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950"></iframe>
                            @else
                                <p class="text-sm text-zinc-500">Inline preview is not available for this file type. Use download instead.</p>
                            @endif
                        @else
                            <p class="text-sm text-zinc-500">This file type cannot be previewed here. Use download instead.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
