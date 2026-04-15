<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">PCN Cases</h2>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>PCN Number</flux:table.column>
                <flux:table.column>Date of Contravention</flux:table.column>
                <flux:table.column>Full Amount</flux:table.column>
                <flux:table.column>Closed</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($cases as $pcn)
                    <flux:table.row wire:key="pcn-{{ $pcn->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.pcn.show', $pcn) }}" class="font-medium text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                                {{ $pcn->pcn_number ?? '#'.$pcn->id }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $pcn->date_of_contravention ? \Carbon\Carbon::parse($pcn->date_of_contravention)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">&pound;{{ number_format($pcn->full_amount ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($pcn->isClosed)
                                <flux:badge color="green" size="sm">Yes</flux:badge>
                            @else
                                <flux:badge color="amber" size="sm">Open</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No PCN cases found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
