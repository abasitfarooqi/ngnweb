<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.careers.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Careers</a>
                <span>/</span>
                <span>{{ $career ? 'Edit' : 'New job posting' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $career ? 'Edit: '.$career->job_title : 'New job posting' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.careers.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save posting</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Job details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Job title" required :error="$errors->first('form.job_title')" class="md:col-span-2">
                    <flux:input wire:model="form.job_title" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Employment type" :error="$errors->first('form.employment_type')">
                    <flux:input wire:model="form.employment_type" placeholder="e.g. Full-time, Part-time" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Location" :error="$errors->first('form.location')">
                    <flux:input wire:model="form.location" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Salary" :error="$errors->first('form.salary')">
                    <flux:input wire:model="form.salary" placeholder="e.g. £25,000–£30,000" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Contact email" :error="$errors->first('form.contact_email')">
                    <flux:input type="email" wire:model="form.contact_email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Posted date" :error="$errors->first('form.job_posted')">
                    <flux:input type="date" wire:model="form.job_posted" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Expiry date" :error="$errors->first('form.expire_date')">
                    <flux:input type="date" wire:model="form.expire_date" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Description" :error="$errors->first('form.description')">
                    <flux:textarea wire:model="form.description" rows="6" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_active" class="accent-zinc-900 dark:accent-zinc-200"> Active (visible on site)
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.careers.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save posting</flux:button>
        </div>
    </form>
</div>
