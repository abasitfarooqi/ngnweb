<div class="border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wide text-zinc-800 dark:text-zinc-200">Weekly rental price</h3>
            <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Current price</p>
                    <p class="font-semibold text-zinc-900 dark:text-white">£{{ number_format($currentWeeklyRent, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Due outstanding</p>
                    <p class="font-semibold text-zinc-900 dark:text-white">
                        {{ $unpaidInvoiceCount }} invoice(s), £{{ number_format($unpaidInvoiceTotal, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="saveWeeklyRent" class="flex w-full flex-col gap-2 sm:w-auto sm:min-w-[24rem] sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-semibold text-zinc-700 dark:text-zinc-300">New weekly price (£)</label>
                <input
                    wire:model="weeklyRent"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-red dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                />
                @error('weeklyRent') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <flux:button type="submit" variant="primary" class="whitespace-nowrap">Update price</flux:button>
        </form>
    </div>

    @if($flashMessage)
        <div class="mt-3 border px-3 py-2 text-xs font-medium
            {{ $flashType === 'success' ? 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'border-red-300 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
            {{ $flashMessage }}
        </div>
    @endif
</div>
