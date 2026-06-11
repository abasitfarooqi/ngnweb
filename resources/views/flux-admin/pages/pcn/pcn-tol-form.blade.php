<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.pcn-tol-requests.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">PCN TOL requests</a>
                <span>/</span>
                <span>{{ $recordId ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $recordId ? 'Edit TOL request' : 'New TOL request' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.pcn-tol-requests.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">TOL details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="PCN case update ID" required :error="$errors->first('form.update_id')">
                    <flux:input type="number" wire:model.live.debounce.300ms="form.update_id" placeholder="Update record ID" />
                </x-flux-admin::field-group>
                @if($updateDisplay !== '')
                    <div class="sm:col-span-2 border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-900 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-200">
                        {{ $updateDisplay }}
                    </div>
                @endif
                <x-flux-admin::field-group label="Request date" required :error="$errors->first('form.request_date')">
                    <flux:input type="date" wire:model="form.request_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Status" required :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="sent">Sent</flux:select.option>
                        <flux:select.option value="approved">Approved</flux:select.option>
                        <flux:select.option value="rejected">Rejected</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Letter sent at" :error="$errors->first('form.letter_sent_at')">
                    <flux:input type="datetime-local" wire:model="form.letter_sent_at" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="3" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.pcn-tol-requests.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
