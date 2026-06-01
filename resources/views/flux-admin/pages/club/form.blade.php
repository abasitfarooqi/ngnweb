<div>
    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.club.index') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Club Members</a>
                <span>/</span>
                <span>{{ $clubMemberId ? 'Edit member' : 'New member' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $clubMemberId ? 'Edit club member' : 'New club member' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.club.index') }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save member</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Member details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Full name" required :error="$errors->first('form.full_name')" class="sm:col-span-2">
                    <flux:input wire:model="form.full_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VRM" :error="$errors->first('form.vrm')">
                    <flux:input wire:model="form.vrm" placeholder="Registration plate" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Make" :error="$errors->first('form.make')">
                    <flux:input wire:model="form.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('form.model')">
                    <flux:input wire:model="form.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input type="number" wire:model="form.year" min="1990" max="2100" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Passkey" :error="$errors->first('form.passkey')">
                    <flux:input wire:model="form.passkey" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_active" class="accent-zinc-900 dark:accent-zinc-200"> Active
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_partner" class="accent-zinc-900 dark:accent-zinc-200"> Partner
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.club.index') }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save member</flux:button>
        </div>
    </form>
</div>
