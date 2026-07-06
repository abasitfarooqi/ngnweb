<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.support-messages.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Support messages</a>
                <span>/</span>
                <span>{{ $supportMessage && $supportMessage->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $supportMessage && $supportMessage->exists ? 'Edit message' : 'New support message' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.support-messages.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Message details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Conversation ID" required :error="$errors->first('form.conversation_id')">
                    <flux:input type="number" wire:model="form.conversation_id" placeholder="e.g. 5" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sender type" required :error="$errors->first('form.sender_type')">
                    <flux:select wire:model="form.sender_type">
                        <flux:select.option value="staff">Staff</flux:select.option>
                        <flux:select.option value="customer">Customer</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sender user ID (staff)" :error="$errors->first('form.sender_user_id')">
                    <flux:input type="number" wire:model="form.sender_user_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Sender customer auth ID" :error="$errors->first('form.sender_customer_auth_id')">
                    <flux:input type="number" wire:model="form.sender_customer_auth_id" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Body" required :error="$errors->first('form.body')">
                    <flux:textarea wire:model="form.body" rows="5" placeholder="Message body…" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.support-messages.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
