<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Appealed?</flux:table.column>
                <flux:table.column>Paid by Owner?</flux:table.column>
                <flux:table.column>Paid by Keeper?</flux:table.column>
                <flux:table.column>Transferred?</flux:table.column>
                <flux:table.column>Additional Fee</flux:table.column>
                <flux:table.column>Cancelled?</flux:table.column>
                <flux:table.column>Note</flux:table.column>
                <flux:table.column>User</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($updates as $row)
                    <flux:table.row wire:key="update-{{ $row->id }}">
                        <flux:table.cell>{{ $row->update_date ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($row->is_appealed)
                                <flux:badge color="amber" size="sm">Yes</flux:badge>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">No</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($row->is_paid_by_owner)
                                <flux:badge color="green" size="sm">Yes</flux:badge>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">No</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($row->is_paid_by_keeper)
                                <flux:badge color="green" size="sm">Yes</flux:badge>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">No</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($row->is_transferred)
                                <flux:badge color="blue" size="sm">Yes</flux:badge>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">No</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($row->additional_fee)
                                £{{ number_format($row->additional_fee, 2) }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($row->is_cancled)
                                <flux:badge color="red" size="sm">Yes</flux:badge>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">No</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="max-w-[200px] truncate">{{ $row->note ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->user?->first_name ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No updates found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
