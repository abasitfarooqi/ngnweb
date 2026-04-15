<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        @if($registrations->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Registration Number</flux:table.column>
                    <flux:table.column>Start Date</flux:table.column>
                    <flux:table.column>End Date</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($registrations as $reg)
                        <flux:table.row wire:key="reg-{{ $reg->id }}">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $reg->registration_number }}</flux:table.cell>
                            <flux:table.cell>{{ $reg->start_date ? \Carbon\Carbon::parse($reg->start_date)->format('d M Y') : '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $reg->end_date ? \Carbon\Carbon::parse($reg->end_date)->format('d M Y') : '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No registration records found.
            </div>
        @endif
    </div>
</div>
