<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.support-conversations.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Support conversations</a>
                <span>/</span>
                <span>{{ $supportConversation && $supportConversation->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $supportConversation && $supportConversation->exists ? 'Edit conversation' : 'New conversation' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.support-conversations.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Conversation details</h2>
            <div class="space-y-4">
                <x-flux-admin::field-group label="Title" :error="$errors->first('form.title')">
                    <flux:input wire:model="form.title" placeholder="Subject of the conversation" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Topic" :error="$errors->first('form.topic')">
                    <flux:input wire:model="form.topic" placeholder="e.g. billing, repairs" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Status" required :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="open">Open</flux:select.option>
                        <flux:select.option value="closed">Closed</flux:select.option>
                        <flux:select.option value="archived">Archived</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer auth ID" :error="$errors->first('form.customer_auth_id')">
                    <flux:input type="number" wire:model="form.customer_auth_id" placeholder="Optional" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.support-conversations.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
