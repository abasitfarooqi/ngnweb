<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.calendar.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Calendar</a>
                <span>/</span>
                <span>{{ $calendarEvent ? 'Edit' : 'New event' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $calendarEvent ? 'Edit event: '.$calendarEvent->title : 'New calendar event' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.calendar.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save event</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Event details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Title" required :error="$errors->first('form.title')">
                    <flux:input wire:model="form.title" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Start" required :error="$errors->first('form.start')">
                    <flux:input type="datetime-local" wire:model="form.start" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="End" :error="$errors->first('form.end')">
                    <flux:input type="datetime-local" wire:model="form.end" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Background colour" :error="$errors->first('form.background_color')">
                    <flux:input wire:model="form.background_color" placeholder="#2563eb" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Text colour" :error="$errors->first('form.text_color')">
                    <flux:input wire:model="form.text_color" placeholder="#ffffff" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.calendar.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save event</flux:button>
        </div>
    </form>
</div>
