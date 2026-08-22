<div
    x-data="{
        bookingId: @entangle('form.booking_id'),
        recordedAt: @entangle('form.recorded_at'),
        fileName: '',
        uploading: false,
        maxBytes: {{ (int) $maxUploadBytes }}
    }"
    x-on:pageshow.window="uploading = false"
>
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
            @if($serviceVideo && $serviceVideo->exists)
                <flux:button type="button" wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
            @else
                <button
                    type="submit"
                    form="service-video-native-form"
                    class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium bg-zinc-800 text-white hover:bg-zinc-700 disabled:opacity-60"
                    x-bind:disabled="uploading"
                >Save</button>
            @endif
        </div>
    </div>

    @if (session('error'))
        <div class="mb-4 border border-red-400 bg-red-50 p-3 text-sm text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Video details</h2>

            <div class="space-y-4">
                <x-flux-admin::field-group label="Booking" required :error="$errors->first('form.booking_id') ?: $errors->first('booking_id')">
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

                <x-flux-admin::field-group label="Recorded at" required :error="$errors->first('form.recorded_at') ?: $errors->first('recorded_at')">
                    <flux:input type="datetime-local" wire:model="form.recorded_at" />
                </x-flux-admin::field-group>

                @if($serviceVideo && $serviceVideo->exists)
                    <x-flux-admin::field-group label="Video file">
                        @if($serviceVideo->video_path)
                            <p class="text-xs text-zinc-500">
                                Current:
                                <a href="{{ $serviceVideo->video_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline dark:text-blue-400">Open video ↗</a>
                            </p>
                        @endif
                        <p class="mt-1 text-xs text-zinc-500">Replace a file from the booking issuance tab.</p>
                    </x-flux-admin::field-group>
                @endif
            </div>
        </div>

        @if($serviceVideo && $serviceVideo->exists)
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('flux-admin.service-videos.index') }}">
                    <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
                </a>
                <flux:button type="button" wire:click="save" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        @else
            <form
                id="service-video-native-form"
                method="POST"
                action="{{ route('flux-admin.service-videos.store') }}"
                enctype="multipart/form-data"
                wire:ignore
                class="space-y-6"
                x-on:submit="
                    const file = $refs.videoInput && $refs.videoInput.files[0] ? $refs.videoInput.files[0] : null;
                    if (! bookingId) {
                        $event.preventDefault();
                        alert('Choose a booking first.');
                        return;
                    }
                    if (! file) {
                        $event.preventDefault();
                        alert('Choose a video file first.');
                        return;
                    }
                    if (file.size > maxBytes) {
                        $event.preventDefault();
                        alert('That video is larger than {{ $maxUploadLabel }}. Compress it or pick a shorter clip.');
                        return;
                    }
                    uploading = true;
                "
            >
                @csrf
                <input type="hidden" name="booking_id" x-bind:value="bookingId">
                <input type="hidden" name="recorded_at" x-bind:value="recordedAt">

                <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
                    <x-flux-admin::field-group label="Video file" required :error="$errors->first('video')">
                        <input
                            type="file"
                            name="video"
                            x-ref="videoInput"
                            accept="video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-matroska"
                            class="text-sm"
                            required
                            x-on:change="
                                const file = $event.target.files[0];
                                fileName = file ? file.name : '';
                                if (file && file.size > maxBytes) {
                                    alert('That video is larger than {{ $maxUploadLabel }}. Compress it or pick a shorter clip.');
                                    $event.target.value = '';
                                    fileName = '';
                                }
                            "
                        />
                        <p class="mt-1 text-xs text-zinc-500" x-show="fileName" x-text="fileName"></p>
                        <p class="mt-1 text-xs text-zinc-500" x-show="uploading">Uploading video… keep this page open.</p>
                        <p class="mt-1 text-xs text-zinc-500">MP4, MOV, AVI, WMV, or MKV. Max {{ $maxUploadLabel }}.</p>
                    </x-flux-admin::field-group>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('flux-admin.service-videos.index') }}">
                        <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium bg-zinc-800 text-white hover:bg-zinc-700 disabled:opacity-60"
                        x-bind:disabled="uploading"
                    >
                        <span x-show="! uploading">Save</span>
                        <span x-cloak x-show="uploading">Uploading…</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
