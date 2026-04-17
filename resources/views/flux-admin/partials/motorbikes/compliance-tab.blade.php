<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-x-auto">
        @if($records->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Year</flux:table.column>
                    <flux:table.column>MOT Status</flux:table.column>
                    <flux:table.column>Tax Status</flux:table.column>
                    <flux:table.column>Insurance Status</flux:table.column>
                    <flux:table.column>MOT Due</flux:table.column>
                    <flux:table.column>Tax Due</flux:table.column>
                    <flux:table.column>Insurance Due</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($records as $record)
                        @php
                            $motDue = $record->mot_due_date ? \Carbon\Carbon::parse($record->mot_due_date) : null;
                            $taxDue = $record->tax_due_date ? \Carbon\Carbon::parse($record->tax_due_date) : null;
                            $insDue = $record->insurance_due_date ? \Carbon\Carbon::parse($record->insurance_due_date) : null;

                            $statusColour = function ($date) {
                                if (!$date) return 'zinc';
                                $days = now()->diffInDays($date, false);
                                if ($days < 0) return 'red';
                                if ($days <= 30) return 'amber';
                                return 'green';
                            };
                        @endphp

                        <flux:table.row wire:key="compliance-{{ $record->id }}">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $record->year }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$statusColour($motDue)" size="sm">{{ $record->getRawOriginal('mot_status') ?: '—' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$statusColour($taxDue)" size="sm">{{ $record->getRawOriginal('road_tax_status') ?: '—' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$statusColour($insDue)" size="sm">{{ $record->insurance_status ?: '—' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $motDue?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $taxDue?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $insDue?->format('d M Y') ?? '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No compliance records found.
            </div>
        @endif
    </div>
</div>
