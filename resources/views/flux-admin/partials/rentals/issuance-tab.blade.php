<div>
    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    @php
        $state = $booking->state ?? '';
        $canIssue = $state === 'Completed';
        $canReissue = $state === 'Completed & Issued';
        $notReady = ! $canIssue && ! $canReissue;
    @endphp

    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex flex-wrap items-center gap-4">
        <div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Booking State</p>
            <flux:badge size="sm" :color="$canIssue ? 'amber' : ($canReissue ? 'emerald' : 'zinc')">{{ $state ?: 'Unknown' }}</flux:badge>
        </div>
        @if($activeItem)
            <div class="border-l border-zinc-200 dark:border-zinc-700 pl-3">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Active Motorbike</p>
                <p class="text-sm font-semibold font-mono text-zinc-900 dark:text-white">
                    {{ $activeItem->motorbike?->reg_no ?? '—' }}
                    <span class="font-normal text-zinc-500 text-xs">{{ $activeItem->motorbike?->make }} {{ $activeItem->motorbike?->model }}</span>
                </p>
            </div>
        @endif
        <div class="border-l border-zinc-200 dark:border-zinc-700 pl-3">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Signed agreements</p>
            <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                {{ $signedVerifiedCount }}/{{ $signedCount }} verified
                @if($signedCount === 0)
                    <span class="font-normal text-amber-600 text-xs">(none yet — use Agreement tab)</span>
                @endif
            </p>
        </div>
    </div>

    @if($notReady)
        <div class="p-8 text-center">
            <flux:icon name="exclamation-triangle" variant="outline" class="w-10 h-10 mx-auto text-amber-500 mb-3" />
            <p class="text-base font-semibold text-zinc-700 dark:text-zinc-200">Issuance not available yet</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 max-w-xl mx-auto">
                Legacy order: verify mandatory documents → <strong>Documents Completed</strong> → clear payments → customer signs agreement → state becomes <strong>Completed</strong> → then <strong>ISSUE NOW</strong>.
            </p>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2">
                Current state: <strong>{{ $state ?: 'Unknown' }}</strong>
            </p>
            <div class="mt-4 flex flex-wrap justify-center gap-2">
                <flux:button size="sm" variant="ghost" wire:click="$dispatch('set-rental-tab', { tab: 'documents' })" class="!rounded-none">Documents</flux:button>
                <flux:button size="sm" variant="ghost" wire:click="$dispatch('set-rental-tab', { tab: 'agreement' })" class="!rounded-none">Agreement</flux:button>
            </div>
        </div>
    @else
        <div class="p-4 md:p-6">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-1">
                @if($canIssue) Issue motorbike to customer @else Inspect &amp; log re-issuance @endif
            </h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-5">
                @if($canIssue)
                    Complete all fields, then press <strong>ISSUE NOW</strong>. State moves to <em>Completed &amp; Issued</em>.
                @else
                    Log a further inspection. Booking stays <em>Completed &amp; Issued</em>.
                @endif
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Person who issued <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="issuanceNotes"
                        type="text"
                        class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. basit"
                    />
                    @error('issuanceNotes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Current mileage <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="currentMileage"
                        type="number"
                        min="0"
                        class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter odometer reading"
                    />
                    @error('currentMileage') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-5">
                <button
                    type="button"
                    wire:click="$toggle('showExtras')"
                    class="w-full sm:w-auto px-4 py-2 text-sm font-semibold border border-sky-400 bg-sky-50 text-sky-900 dark:bg-sky-900/20 dark:text-sky-200 dark:border-sky-700"
                >
                    {{ $showExtras ? 'Hide maintenance &amp; video' : 'Click if you want to Upload Video or Add Maintenance Log' }}
                </button>
            </div>

            @if($showExtras)
                <div class="mb-5 border border-zinc-200 dark:border-zinc-700 p-4 space-y-5">
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Service video</h4>
                        <form
                            method="POST"
                            action="{{ route('flux-admin.rentals.service-videos.store', $booking) }}"
                            enctype="multipart/form-data"
                            class="max-w-full space-y-2"
                            wire:ignore
                            x-data="{ fileName: '', uploading: false, maxBytes: {{ \App\Support\UploadLimit::maxBytes() }} }"
                            x-on:pageshow.window="uploading = false"
                            x-on:submit="
                                const input = $refs.videoInput;
                                const file = input && input.files && input.files[0] ? input.files[0] : null;
                                if (! file) {
                                    $event.preventDefault();
                                    return;
                                }
                                if (file.size > maxBytes) {
                                    $event.preventDefault();
                                    alert('That video is larger than {{ \App\Support\UploadLimit::label() }}. Compress it or pick a shorter clip.');
                                    return;
                                }
                                uploading = true;
                            "
                        >
                            @csrf
                            <label
                                for="issuance-video-file-{{ $booking->id }}"
                                class="inline-flex max-w-full cursor-pointer items-center border border-emerald-500 bg-white px-3 py-2 text-sm font-medium text-zinc-900 hover:bg-emerald-50 dark:bg-zinc-900 dark:text-white dark:hover:bg-emerald-900/20"
                            >
                                <span class="truncate">Choose video file</span>
                            </label>
                            <input
                                id="issuance-video-file-{{ $booking->id }}"
                                name="video"
                                type="file"
                                accept="video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-matroska"
                                class="sr-only"
                                required
                                x-ref="videoInput"
                                x-on:change="
                                    const file = $event.target.files[0];
                                    fileName = file ? file.name : '';
                                    if (file && file.size > maxBytes) {
                                        alert('That video is larger than {{ \App\Support\UploadLimit::label() }}. Compress it or pick a shorter clip.');
                                        $event.target.value = '';
                                        fileName = '';
                                    }
                                "
                            />

                            <div class="min-h-5 max-w-full text-xs text-zinc-500 dark:text-zinc-400">
                                <p class="max-w-full truncate" x-text="fileName || 'No video selected.'"></p>
                            </div>

                            @error('video') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-zinc-500">MP4, MOV, AVI, WMV, or MKV. Max {{ \App\Support\UploadLimit::label() }}.</p>

                            <button
                                type="submit"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-60"
                                x-bind:disabled="uploading || ! fileName"
                            >
                                <span x-show="! uploading">Upload video</span>
                                <span x-cloak x-show="uploading">Uploading… keep this page open</span>
                            </button>
                        </form>
                        <p class="mt-2">
                            <a href="{{ route('flux-admin.service-videos.create', ['booking_id' => $booking->id]) }}" class="text-xs text-blue-600 hover:underline dark:text-blue-400">Upload via service videos page ↗</a>
                        </p>
                        @if($videos->isNotEmpty())
                            <ul class="mt-3 text-xs text-zinc-500 space-y-1">
                                @foreach($videos as $video)
                                    <li wire:key="vid-{{ $video->id }}">
                                        <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline dark:text-blue-400">{{ basename($video->video_path) }}</a>
                                        — {{ $video->recorded_at ? \Carbon\Carbon::parse($video->recorded_at)->format('d M Y H:i') : '—' }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    @if($activeItem)
                        <div>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Maintenance log</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <textarea wire:model="logDescription" maxlength="1500" rows="4" placeholder="Description (up to 1500 characters)" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"></textarea>
                                <input wire:model="logCost" type="number" step="0.01" min="0" placeholder="Cost (£)" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                                <input wire:model="logServicedAt" type="date" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                                <input wire:model="logNote" type="text" placeholder="Note (optional)" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            </div>
                            @error('logDescription') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            @error('logCost') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            @error('logServicedAt') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            <flux:button size="sm" wire:click="addMaintenanceLog" class="!rounded-none">Save maintenance log</flux:button>
                            @if($maintenanceLogs->isNotEmpty())
                                <ul class="mt-3 text-xs text-zinc-500 space-y-1">
                                    @foreach($maintenanceLogs as $log)
                                        <li wire:key="ml-{{ $log->id }}">{{ $this->formatMaintenanceDate($log->serviced_at) }} — {{ $log->description }} (£{{ number_format((float) $log->cost, 2) }})</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <div class="mb-5">
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Issue from branch <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-wrap gap-4">
                    @foreach(['Catford', 'Tooting', 'Sutton'] as $branch)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="issuanceBranch" type="radio" value="{{ $branch }}" class="w-4 h-4" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $branch }}</span>
                        </label>
                    @endforeach
                </div>
                @error('issuanceBranch') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5 space-y-3">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input wire:model="isVideoRecorded" type="checkbox" class="mt-0.5 w-5 h-5" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300">I have recorded video.</span>
                </label>
                @error('isVideoRecorded') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                <label class="flex items-start gap-3 cursor-pointer">
                    <input wire:model="accessoriesChecked" type="checkbox" class="mt-0.5 w-5 h-5" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300">I have checked the accessories</span>
                </label>
                @error('accessoriesChecked') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                @if($canReissue)
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input wire:model="isInsured" type="checkbox" class="mt-0.5 w-5 h-5" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">
                            I have checked insurance on AskMID.
                            <a href="https://enquiry.navigate.mib.org.uk/checkyourvehicle" target="_blank" class="text-blue-600 dark:text-blue-400 underline text-xs ml-1">Check here ↗</a>
                        </span>
                    </label>
                @endif
            </div>

            @if($canIssue)
                <button
                    type="button"
                    wire:click="issueMotorbike"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-60"
                >
                    ISSUE NOW
                </button>
            @else
                <button
                    type="button"
                    wire:click="reissueMotorbike"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-zinc-800 hover:bg-zinc-900 text-white transition disabled:opacity-60"
                >
                    SAVE INSPECTION LOG
                </button>
            @endif
        </div>
    @endif

    @if($issuanceHistory->isNotEmpty())
        <div class="border-t border-zinc-200 dark:border-zinc-700">
            <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50">
                <h4 class="text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Issuance / inspection history</h4>
            </div>
            <div class="touch-pan-x overflow-x-auto">
                <div class="min-w-[42rem] md:min-w-0">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Date</flux:table.column>
                            <flux:table.column>Person / notes</flux:table.column>
                            <flux:table.column>Mileage</flux:table.column>
                            <flux:table.column>Branch</flux:table.column>
                            <flux:table.column>Checks</flux:table.column>
                            <flux:table.column>Staff user</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($issuanceHistory as $record)
                                <flux:table.row wire:key="iss-{{ $record->id }}">
                                    <flux:table.cell class="text-xs">{{ $record->created_at?->format('d M Y H:i') ?? '—' }}</flux:table.cell>
                                    <flux:table.cell class="text-sm max-w-[18rem] truncate">{{ $record->notes ?? '—' }}</flux:table.cell>
                                    <flux:table.cell>{{ number_format((int) $record->current_mileage) }}</flux:table.cell>
                                    <flux:table.cell>{{ $record->issuance_branch ?? '—' }}</flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex gap-1 flex-wrap">
                                            <flux:badge size="sm" :color="$record->is_video_recorded ? 'emerald' : 'red'">Video</flux:badge>
                                            <flux:badge size="sm" :color="$record->accessories_checked ? 'emerald' : 'red'">Accessories</flux:badge>
                                            @if($record->is_insured)
                                                <flux:badge size="sm" color="emerald">Insured</flux:badge>
                                            @endif
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell class="text-xs">{{ $record->issuedBy?->first_name ?? '—' }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>
        </div>
    @endif
</div>
