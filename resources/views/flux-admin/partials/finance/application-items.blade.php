<div>
    @if($items->isEmpty())
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            No application items found.
        </div>
    @else
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Motorbike</flux:table.column>
                    <flux:table.column>Start Date</flux:table.column>
                    <flux:table.column>Due Date</flux:table.column>
                    <flux:table.column>End Date</flux:table.column>
                    <flux:table.column>Weekly Instalment</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($items as $item)
                        <flux:table.row wire:key="ai-{{ $item->id }}">
                            <flux:table.cell>
                                @if($item->motorbike)
                                    <span class="font-mono text-xs">{{ $item->motorbike->reg_no }}</span>
                                    <span class="text-zinc-500 dark:text-zinc-400 text-xs ml-1">{{ $item->motorbike->make }} {{ $item->motorbike->model }}</span>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell>£{{ number_format($item->weekly_instalment ?? 0, 2) }}</flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $item->user?->first_name ?? '—' }} {{ $item->user?->last_name ?? '' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif
</div>
