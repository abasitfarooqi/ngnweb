<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Queue monitor</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Delayed jobs in the Redis queue.</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:select wire:model.live="queueName" class="w-40">
                <flux:select.option value="default">default</flux:select.option>
                <flux:select.option value="emails">emails</flux:select.option>
                <flux:select.option value="notifications">notifications</flux:select.option>
                <flux:select.option value="judopay">judopay</flux:select.option>
            </flux:select>
            <flux:button icon="arrow-path" wire:click="$refresh" size="sm" variant="ghost" class="!rounded-none">Refresh</flux:button>
        </div>
    </div>

    @if($error)
        <div class="border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700 p-4 text-red-700 dark:text-red-300 text-sm">
            {{ $error }}
        </div>
    @endif

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
        @if(count($jobs))
            <div class="px-5 py-3 border-b border-zinc-200 dark:border-zinc-800 text-sm text-zinc-500 dark:text-zinc-400">
                {{ count($jobs) }} delayed {{ Str::plural('job', count($jobs)) }} in <span class="font-mono font-medium text-zinc-900 dark:text-white">{{ $queueName }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Job</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Queue</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Attempts</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobs as $job)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="px-4 py-3 font-mono text-xs text-zinc-900 dark:text-white">{{ $job['name'] }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $job['queue'] }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $job['attempts'] }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $job['available_human'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 text-center text-zinc-500 dark:text-zinc-400">
                No delayed jobs in the <span class="font-mono font-medium">{{ $queueName }}</span> queue.
            </div>
        @endif
    </div>
</div>
