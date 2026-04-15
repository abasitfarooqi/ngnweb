<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Request Date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Letter Sent</flux:table.column>
                <flux:table.column>Note</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>PDF</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($requests as $row)
                    <flux:table.row wire:key="tol-{{ $row->id }}">
                        <flux:table.cell>{{ $row->request_date ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($row->status)
                                <flux:badge
                                    :color="match(strtolower($row->status)) {
                                        'approved' => 'green',
                                        'pending' => 'amber',
                                        'rejected', 'denied' => 'red',
                                        default => 'zinc',
                                    }"
                                    size="sm"
                                >
                                    {{ ucfirst($row->status) }}
                                </flux:badge>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->letter_sent_at ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="max-w-[200px] truncate">{{ $row->note ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->user?->first_name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($row->full_path)
                                <a href="{{ $row->full_path }}" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                    View PDF ↗
                                </a>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No TOL requests found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
