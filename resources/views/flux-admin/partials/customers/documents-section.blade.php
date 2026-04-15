<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Documents</h2>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Document Type</flux:table.column>
                <flux:table.column>File Name</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Valid Until</flux:table.column>
                <flux:table.column>Verified</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($documents as $doc)
                    <flux:table.row wire:key="doc-{{ $doc->id }}">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $doc->documentType?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                            @if($doc->file_url)
                                <a href="{{ $doc->file_url }}" target="_blank" class="hover:text-zinc-900 dark:hover:text-white transition underline">{{ $doc->file_name ?? 'View' }}</a>
                            @else
                                {{ $doc->file_name ?? '—' }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                                $sColour = match($doc->status) {
                                    'approved' => 'green',
                                    'pending' => 'amber',
                                    'rejected' => 'red',
                                    default => 'zinc',
                                };
                            @endphp
                            <flux:badge :color="$sColour" size="sm">{{ ucfirst($doc->status ?? 'unknown') }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $doc->valid_until ? \Carbon\Carbon::parse($doc->valid_until)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($doc->is_verified)
                                <flux:badge color="green" size="sm">Yes</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">No</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No documents on file.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
