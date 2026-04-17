<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-x-auto">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Finance Applications</h2>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Contract Date</flux:table.column>
                <flux:table.column>Weekly Instalment</flux:table.column>
                <flux:table.column>Deposit</flux:table.column>
                <flux:table.column>Motorbike Price</flux:table.column>
                <flux:table.column>Cancelled</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($applications as $app)
                    <flux:table.row wire:key="finance-{{ $app->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.finance.show', $app) }}" class="font-medium text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                                #{{ $app->id }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $app->contract_date ? \Carbon\Carbon::parse($app->contract_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">&pound;{{ number_format($app->weekly_instalment ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">&pound;{{ number_format($app->deposit ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">&pound;{{ number_format($app->motorbike_price ?? 0, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($app->is_cancelled)
                                <flux:badge color="red" size="sm">Yes</flux:badge>
                            @else
                                <flux:badge color="green" size="sm">No</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No finance applications found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
