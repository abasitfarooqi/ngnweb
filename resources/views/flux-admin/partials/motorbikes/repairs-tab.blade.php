<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        @if($repairs->count())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column>Notes</flux:table.column>
                    <flux:table.column>Repaired?</flux:table.column>
                    <flux:table.column>Returned?</flux:table.column>
                    <flux:table.column>Branch</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($repairs as $repair)
                        <flux:table.row wire:key="repair-{{ $repair->id }}">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $repair->id }}</flux:table.cell>
                            <flux:table.cell class="max-w-xs truncate">{{ \Illuminate\Support\Str::limit($repair->notes, 60) }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$repair->is_repaired ? 'green' : 'red'" size="sm">
                                    {{ $repair->is_repaired ? 'Yes' : 'No' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$repair->is_returned ? 'green' : 'amber'" size="sm">
                                    {{ $repair->is_returned ? 'Yes' : 'No' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $repair->branch?->name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button wire:click="toggleRepair({{ $repair->id }})" size="xs" variant="subtle" icon="{{ $expandedRepairId === $repair->id ? 'chevron-up' : 'chevron-down' }}">
                                    {{ $expandedRepairId === $repair->id ? 'Hide' : 'Show' }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>

                        @if($expandedRepairId === $repair->id)
                            <flux:table.row wire:key="repair-detail-{{ $repair->id }}">
                                <flux:table.cell colspan="6">
                                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900 space-y-4">
                                        {{-- Contact info --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                            <div>
                                                <span class="text-zinc-500 dark:text-zinc-400">Contact:</span>
                                                <span class="ml-1 text-zinc-900 dark:text-white">{{ $repair->fullname ?: '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-zinc-500 dark:text-zinc-400">Email:</span>
                                                <span class="ml-1 text-zinc-900 dark:text-white">{{ $repair->email ?: '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-zinc-500 dark:text-zinc-400">Phone:</span>
                                                <span class="ml-1 text-zinc-900 dark:text-white">{{ $repair->phone ?: '—' }}</span>
                                            </div>
                                        </div>

                                        {{-- Updates --}}
                                        @if($repair->updates->count())
                                            <div>
                                                <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2">Updates</h4>
                                                <div class="space-y-2">
                                                    @foreach($repair->updates as $update)
                                                        <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-3">
                                                            <p class="text-sm text-zinc-900 dark:text-white">{{ $update->job_description }}</p>
                                                            @if($update->price)
                                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Price: £{{ number_format($update->price, 2) }}</p>
                                                            @endif
                                                            @if($update->note)
                                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Note: {{ $update->note }}</p>
                                                            @endif
                                                            @if($update->services->count())
                                                                <div class="flex flex-wrap gap-1 mt-2">
                                                                    @foreach($update->services as $service)
                                                                        <flux:badge color="zinc" size="sm">{{ $service->name ?? $service->service_name ?? 'Service' }}</flux:badge>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Observations --}}
                                        @if($repair->observations->count())
                                            <div>
                                                <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2">Observations</h4>
                                                <div class="space-y-1">
                                                    @foreach($repair->observations as $obs)
                                                        <p class="text-sm text-zinc-700 dark:text-zinc-300">• {{ $obs->observation ?? $obs->note ?? $obs->description ?? '—' }}</p>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endif
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No repair records found.
            </div>
        @endif
    </div>
</div>
