<div>
    @if($documents->isEmpty())
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            No documents found for this application.
        </div>
    @else
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>File Name</flux:table.column>
                    <flux:table.column>Verified</flux:table.column>
                    <flux:table.column>File Path</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($documents as $doc)
                        <flux:table.row wire:key="doc-{{ $doc->id }}">
                            <flux:table.cell>{{ $doc->file_name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                @if($doc->is_verified)
                                    <flux:badge color="green" size="sm">Verified</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Unverified</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($doc->file_path)
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-mono truncate max-w-xs inline-block">
                                        {{ $doc->file_path }}
                                    </a>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif
</div>
