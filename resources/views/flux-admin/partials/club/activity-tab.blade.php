<div>
    @if($invoiceNotFound)
        <div class="border border-zinc-200 bg-white p-8 text-center text-sm text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
            POS invoice not found for this member.
        </div>
    @elseif($timeline->count())
        <div class="club-activity-feed border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($timeline as $entry)
                    <div class="club-activity-row flex items-start gap-4 px-5 py-4{{ ! empty($entry['matched']) ? ' is-hit' : '' }}" @if(! empty($loop->first) && ! empty($entry['matched'])) id="club-pos-invoice-hit" @endif>
                        @php $iconClass = 'club-activity-icon-'.($entry['colour'] ?? 'blue'); @endphp

                        <div class="flex-shrink-0 p-2 {{ $iconClass }}">
                            <flux:icon :name="$entry['icon']" variant="outline" class="w-4 h-4" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="club-activity-type club-activity-type-{{ $entry['colour'] ?? 'blue' }}">{{ $entry['type'] }}</span>
                                <span class="club-activity-title text-sm font-medium">
                                    £{{ number_format($entry['amount'], 2) }}
                                </span>
                            </div>
                            <p class="club-activity-meta mt-1 text-xs">{{ $entry['details'] }}</p>
                        </div>

                        <div class="flex-shrink-0 text-right">
                            <p class="club-activity-title text-sm">
                                {{ $entry['date'] ? $entry['date']->format('d M Y') : '—' }}
                            </p>
                            @if($entry['user'])
                                <p class="club-activity-meta text-xs">{{ $entry['user'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="border border-zinc-200 bg-white p-8 text-center text-sm text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
            No activity found.
        </div>
    @endif
</div>
