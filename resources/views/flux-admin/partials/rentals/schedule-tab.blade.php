<div class="p-4 sm:p-5 space-y-6">
    @if($flashMessage)
        <div class="border px-4 py-3 text-sm font-medium
            {{ $flashType === 'success' ? 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'border-red-300 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
            {{ $flashMessage }}
        </div>
    @endif

    @if(!$hasOpenItem && $endedItem)
        <div class="border border-zinc-300 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/50 px-4 py-3 text-sm">
            <p class="font-semibold text-zinc-900 dark:text-white">Ended rental</p>
            <p class="mt-1 text-zinc-600 dark:text-zinc-300">
                Ended
                {{ \Carbon\Carbon::parse($endedItem->end_date)->format('d M Y') }}
                @if($closing?->collect_time)
                    at {{ \Illuminate\Support\Str::of((string) $closing->collect_time)->substr(0, 5) }}
                @elseif($closing?->collect_date)
                    (collect date {{ \Carbon\Carbon::parse($closing->collect_date)->format('d M Y') }}{{ $closing->collect_time ? ' '.$closing->collect_time : '' }})
                @endif
            </p>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">For a full reopen with a new start date, use the <button type="button" wire:click="$dispatch('set-rental-tab', { tab: 'restart' })" class="underline text-zinc-700 dark:text-zinc-300">Restart</button> tab.</p>
        </div>
    @endif

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 space-y-3">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Invoice schedule</h3>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            Start date sets the weekday for automatic upcoming invoice generation
            (e.g. Thursday start → future unpaid invoices land on Thursdays). Adjust within the same week when the rental started to shift payment day without rewriting history.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Current start</p>
                <p class="font-semibold text-zinc-900 dark:text-white">
                    {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('d M Y') : '—' }}
                    @if($currentWeekday)
                        <span class="font-normal text-zinc-500">({{ $currentWeekday }})</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Status</p>
                <p class="font-semibold text-zinc-900 dark:text-white">{{ $hasOpenItem ? 'Active / open item' : 'Ended' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 space-y-3">
            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Change start date</h4>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Saves start date and rebuilds unpaid future invoices onto that weekday.</p>
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">New start date</label>
                <input type="date" wire:model="newStartDate"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm" />
                @error('newStartDate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <flux:button size="sm" variant="primary" wire:click="saveStartDate" wire:confirm="Update start date and realign unpaid future invoices?" class="!rounded-none">
                Save start date
            </flux:button>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 space-y-3">
            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Adjust invoice weekday</h4>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Moves start date to the chosen day in the same week (Mon–Sat — Sunday blocked), then realigns future invoices.</p>
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Weekday</label>
                <select wire:model="targetWeekday"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm">
                    @foreach($weekdays as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                </select>
                @error('targetWeekday') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <flux:button size="sm" variant="primary" wire:click="adjustWeekday" wire:confirm="Set weekday in the start week and realign unpaid future invoices?" class="!rounded-none">
                Apply weekday
            </flux:button>
        </div>
    </div>
</div>
