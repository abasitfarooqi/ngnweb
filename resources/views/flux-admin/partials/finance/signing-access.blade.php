<div>
    @if($accesses->isEmpty())
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            No signing access records found for this application.
        </div>
    @else
        <div class="space-y-4">
            @foreach($accesses as $access)
                <div wire:key="sa-{{ $access->id }}" class="border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Customer</p>
                            <p class="text-sm text-zinc-900 dark:text-white mt-0.5">
                                {{ $access->customer?->first_name ?? '—' }} {{ $access->customer?->last_name ?? '' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Passcode</p>
                            <p class="text-sm text-zinc-900 dark:text-white mt-0.5 font-mono">{{ $access->passcode ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Expires At</p>
                            <p class="text-sm mt-0.5">
                                @if($access->expires_at)
                                    <span class="{{ $access->expires_at->isPast() ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }}">
                                        {{ $access->expires_at->format('d M Y H:i') }}
                                    </span>
                                    @if($access->expires_at->isPast())
                                        <flux:badge color="red" size="sm" class="ml-1">Expired</flux:badge>
                                    @endif
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
