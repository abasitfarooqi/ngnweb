<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.service-videos.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Service videos</a>
                <span>/</span>
                <span>{{ $serviceVideo && $serviceVideo->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $serviceVideo && $serviceVideo->exists ? 'Edit service video' : 'New service video' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.service-videos.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Video details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Booking ID" required :error="$errors->first('form.booking_id')">
                    <flux:input type="number" wire:model="form.booking_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Recorded at" :error="$errors->first('form.recorded_at')">
                    <flux:input type="date" wire:model="form.recorded_at" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Video path / URL" :error="$errors->first('form.video_path')">
                    <flux:input wire:model="form.video_path" placeholder="e.g. /storage/videos/file.mp4" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.service-videos.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
