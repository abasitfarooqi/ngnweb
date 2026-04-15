<div>
    @if($agreements->isNotEmpty())
        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @foreach($agreements as $agreement)
                <div class="p-5" wire:key="agreement-{{ $agreement->id }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Customer</p>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $agreement->customer?->first_name }} {{ $agreement->customer?->last_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Passcode</p>
                            <p class="text-sm font-mono font-semibold text-zinc-900 dark:text-white">{{ $agreement->passcode }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Expires At</p>
                            <p class="text-sm text-zinc-900 dark:text-white">
                                {{ $agreement->expires_at ? \Carbon\Carbon::parse($agreement->expires_at)->format('d M Y H:i') : 'No expiry' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Agreement Link</p>
                        <a
                            href="{{ url('/agreement/' . $agreement->customer_id . '/' . $agreement->passcode) }}"
                            target="_blank"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all"
                        >
                            {{ url('/agreement/' . $agreement->customer_id . '/' . $agreement->passcode) }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-8 text-center">
            <flux:icon name="document-text" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No agreement access records found for this booking.</p>
        </div>
    @endif
</div>
