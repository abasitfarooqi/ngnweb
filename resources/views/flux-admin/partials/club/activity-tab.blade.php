<div>
    @if($timeline->count())
        <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($timeline as $entry)
                    <div class="flex items-start gap-4 px-5 py-4">
                        @php
                            $colourMap = [
                                'blue'  => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                                'green' => 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400',
                                'amber' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
                            ];
                            $classes = $colourMap[$entry['colour']] ?? $colourMap['blue'];
                        @endphp

                        <div class="flex-shrink-0 p-2 {{ $classes }}">
                            <flux:icon :name="$entry['icon']" variant="outline" class="w-4 h-4" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <flux:badge :color="$entry['colour']" size="sm">{{ $entry['type'] }}</flux:badge>
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">
                                    £{{ number_format($entry['amount'], 2) }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $entry['details'] }}</p>
                        </div>

                        <div class="flex-shrink-0 text-right">
                            <p class="text-sm text-zinc-900 dark:text-white">
                                {{ $entry['date'] ? $entry['date']->format('d M Y') : '—' }}
                            </p>
                            @if($entry['user'])
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $entry['user'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            No activity found.
        </div>
    @endif
</div>
