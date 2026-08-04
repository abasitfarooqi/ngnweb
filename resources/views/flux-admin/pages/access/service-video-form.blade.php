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

            <div class="space-y-4">
                <x-flux-admin::field-group label="Booking" required :error="$errors->first('form.booking_id')">
                    <div class="space-y-2">
                        <div class="flex gap-2">
                            <flux:input
                                wire:model.live.debounce.300ms="bookingSearch"
                                placeholder="Search booking ID, customer name, or reg no…"
                                class="flex-1"
                            />
                            @if($selectedBookingLabel)
                                <flux:button type="button" size="sm" variant="ghost" wire:click="clearBooking" class="!rounded-none">Clear</flux:button>
                            @endif
                        </div>

                        @if($bookingResults->isNotEmpty())
                            <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 max-h-56 overflow-y-auto">
                                @foreach($bookingResults as $booking)
                                    @php
                                        $customer = $booking->customer;
                                        $name = trim(($customer->first_name ?? '').' '.($customer->last_name ?? '')) ?: 'Unknown';
                                        $bike = $booking->rentingBookingItems->first()?->motorbike;
                                    @endphp
                                    <button
                                        type="button"
                                        wire:key="booking-option-{{ $booking->id }}"
                                        wire:click="selectBooking({{ $booking->id }})"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800 last:border-b-0"
                                    >
                                        <span class="font-semibold text-zinc-900 dark:text-white">#{{ $booking->id }} · {{ $name }}</span>
                                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $bike?->reg_no ?? '—' }}
                                            @if($bike?->make || $bike?->model)
                                                · {{ trim(($bike?->make ?? '').' '.($bike?->model ?? '')) }}
                                            @endif
                                            · {{ $booking->start_date?->format('d M Y H:i') ?? '—' }}
                                            @if($customer?->phone)
                                                · {{ $customer->phone }}
                                            @endif
                                            @if($customer?->email)
                                                · {{ $customer->email }}
                                            @endif
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($selectedBookingLabel)
                            <p class="text-xs text-emerald-700 dark:text-emerald-300">Selected: {{ $selectedBookingLabel }}</p>
                        @endif
                    </div>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Recorded at" required :error="$errors->first('form.recorded_at')">
                    <flux:input type="datetime-local" wire:model="form.recorded_at" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Video file" :required="! ($serviceVideo && $serviceVideo->exists)" :error="$errors->first('videoFile')">
                    <input type="file" wire:model="videoFile" accept="video/*" class="text-sm" />
                    @if($serviceVideo && $serviceVideo->exists && $serviceVideo->video_path)
                        <p class="mt-2 text-xs text-zinc-500">
                            Current:
                            <a href="{{ $serviceVideo->video_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline dark:text-blue-400">Open video ↗</a>
                        </p>
                    @endif
                    <p class="mt-1 text-xs text-zinc-500">MP4, MOV, AVI, WMV, or MKV. Max 500 MB.</p>
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
