<div>
    <div class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Motorbike</flux:table.column>
                <flux:table.column>Weekly Rent</flux:table.column>
                <flux:table.column>Start Date</flux:table.column>
                <flux:table.column>Due Date</flux:table.column>
                <flux:table.column>End Date</flux:table.column>
                <flux:table.column>Posted</flux:table.column>
                <flux:table.column>User</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($items as $item)
                    <flux:table.row wire:key="item-{{ $item->id }}">
                        <flux:table.cell>
                            @if($item->motorbike)
                                <span class="font-medium">{{ $item->motorbike->reg_no }}</span>
                                <span class="text-zinc-500 dark:text-zinc-400 text-xs block">{{ $item->motorbike->make }} {{ $item->motorbike->model }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>£{{ number_format($item->weekly_rent, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ $item->start_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $item->due_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($item->end_date)
                                <span class="text-zinc-500 dark:text-zinc-400">{{ $item->end_date->format('d M Y') }}</span>
                            @else
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$item->is_posted ? 'green' : 'amber'" size="sm">
                                {{ $item->is_posted ? 'Yes' : 'No' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $item->user?->first_name ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No booking items found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
