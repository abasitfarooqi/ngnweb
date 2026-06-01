<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.judopay-mit-queue.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Judopay MIT queue</a>
                <span>/</span>
                <span>{{ $recordId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $recordId ? 'Edit MIT queue entry' : 'New MIT queue entry' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.judopay-mit-queue.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Entry details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="NGN MIT queue ID" required :error="$errors->first('form.ngn_mit_queue_id')">
                    <flux:input type="number" wire:model="form.ngn_mit_queue_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Payment reference" :error="$errors->first('form.judopay_payment_reference')">
                    <flux:input wire:model="form.judopay_payment_reference" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Fire date" :error="$errors->first('form.mit_fire_date')">
                    <flux:input type="date" wire:model="form.mit_fire_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Retry count" :error="$errors->first('form.retry')">
                    <flux:input type="number" min="0" wire:model="form.retry" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.fired" class="accent-zinc-900 dark:accent-zinc-200"> Fired
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.cleared" class="accent-zinc-900 dark:accent-zinc-200"> Cleared
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.judopay-mit-queue.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
