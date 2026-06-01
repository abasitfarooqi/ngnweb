<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.employee-schedules.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Employee Schedules</a>
                <span>/</span>
                <span>{{ $employeeSchedule ? 'Edit' : 'New schedule' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $employeeSchedule ? 'Edit schedule #'.$employeeSchedule->id : 'New employee schedule' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.employee-schedules.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save schedule</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Schedule details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Employee" required :error="$errors->first('form.user_id')">
                    <flux:select wire:model="form.user_id" placeholder="Select employee">
                        <flux:select.option value="">Select…</flux:select.option>
                        @foreach($users as $u)
                            <flux:select.option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Day off" required :error="$errors->first('form.off_day')">
                    <flux:input type="date" wire:model="form.off_day" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.employee-schedules.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save schedule</flux:button>
        </div>
    </form>
</div>
