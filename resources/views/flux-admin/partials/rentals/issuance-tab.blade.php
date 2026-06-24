<div>
    {{-- Flash message --}}
    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    @php
        $state = $booking->state ?? '';
        $canIssue    = $state === 'Completed';
        $canReissue  = $state === 'Completed & Issued';
        $notReady    = !$canIssue && !$canReissue;
    @endphp

    {{-- State indicator --}}
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center gap-3">
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
    </div>

    @if($notReady)
        {{-- Not ready to issue --}}
        <div class="p-8 text-center">
            <flux:icon name="exclamation-triangle" variant="outline" class="w-10 h-10 mx-auto text-amber-500 mb-3" />
            <p class="text-base font-semibold text-zinc-700 dark:text-zinc-200">Issuance Not Available</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Booking must be in <strong>"Completed"</strong> state to issue, or <strong>"Completed &amp; Issued"</strong> to log a re-inspection.<br>
                Current state: <strong>{{ $state ?: 'Unknown' }}</strong>
            </p>
        </div>
    @else
        {{-- Issue / Re-issue form --}}
        <div class="p-4 md:p-6">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-1">
                @if($canIssue) Issue Motorbike to Customer @else Inspect &amp; Log Re-issuance @endif
            </h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-5">
                @if($canIssue)
                    Complete all fields before pressing <strong>ISSUE NOW</strong>. This will move the booking to <em>"Completed &amp; Issued"</em>.
                @else
                    Log a new inspection / weekly check. Booking stays in <em>"Completed &amp; Issued"</em> state.
                @endif
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                {{-- Issued by / Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        @if($canIssue) Person Who Issued @else Notes / Inspector Name @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="issuanceNotes"
                        type="text"
                        class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                        placeholder="e.g. John Smith"
                    />
                    @error('issuanceNotes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Current mileage --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Current Mileage <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="currentMileage"
                        type="number"
                        min="0"
                        class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                        placeholder="Enter odometer reading"
                    />
                    @error('currentMileage') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Branch selection --}}
            <div class="mb-5">
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    Issue From Branch <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-wrap gap-4">
                    @foreach(['Catford', 'Tooting', 'Sutton'] as $branch)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                wire:model="issuanceBranch"
                                type="radio"
                                value="{{ $branch }}"
                                class="w-4 h-4 accent-brand-red"
                            />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $branch }}</span>
                        </label>
                    @endforeach
                </div>
                @error('issuanceBranch') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Checkboxes --}}
            <div class="mb-5 space-y-3">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input wire:model="isVideoRecorded" type="checkbox" class="mt-0.5 w-5 h-5 accent-brand-red" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300">
                        I have recorded the handover video.
                        @if($canReissue) <span class="text-zinc-400">(Already recorded previously)</span> @endif
                    </span>
                </label>
                @error('isVideoRecorded') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                <label class="flex items-start gap-3 cursor-pointer">
                    <input wire:model="accessoriesChecked" type="checkbox" class="mt-0.5 w-5 h-5 accent-brand-red" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300">I have checked all accessories.</span>
                </label>
                @error('accessoriesChecked') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                @if($canReissue)
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input wire:model="isInsured" type="checkbox" class="mt-0.5 w-5 h-5 accent-brand-red" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">
                            I have checked insurance on AskMID.
                            <a href="https://enquiry.navigate.mib.org.uk/checkyourvehicle" target="_blank" class="text-blue-600 dark:text-blue-400 underline text-xs ml-1">Check here ↗</a>
                        </span>
                    </label>
                @endif
            </div>

            {{-- Action button --}}
            @if($canIssue)
                <button
                    wire:click="issueMotorbike"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-60"
                >
                    <svg wire:loading wire:target="issueMotorbike" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    ISSUE NOW
                </button>
            @else
                <button
                    wire:click="reissueMotorbike"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-brand-red hover:opacity-90 text-white transition disabled:opacity-60"
                >
                    <svg wire:loading wire:target="reissueMotorbike" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    SAVE INSPECTION LOG
                </button>
            @endif
        </div>
    @endif

    {{-- Issuance History --}}
    @if($issuanceHistory->isNotEmpty())
        <div class="border-t border-zinc-200 dark:border-zinc-700">
            <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50">
                <h4 class="text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Issuance / Inspection History</h4>
            </div>
            <div class="touch-pan-x overflow-x-auto">
                <div class="min-w-[42rem] md:min-w-0">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Date</flux:table.column>
                            <flux:table.column>Notes</flux:table.column>
                            <flux:table.column>Mileage</flux:table.column>
                            <flux:table.column>Branch</flux:table.column>
                            <flux:table.column>Checks</flux:table.column>
                            <flux:table.column>Issued By</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($issuanceHistory as $record)
                                <flux:table.row wire:key="iss-{{ $record->id }}">
                                    <flux:table.cell class="text-xs">{{ $record->created_at?->format('d M Y H:i') ?? '—' }}</flux:table.cell>
                                    <flux:table.cell class="text-sm max-w-[18rem] truncate">{{ $record->notes ?? '—' }}</flux:table.cell>
                                    <flux:table.cell>{{ number_format($record->current_mileage) }}</flux:table.cell>
                                    <flux:table.cell>{{ $record->issuance_branch ?? '—' }}</flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex gap-1 flex-wrap">
                                            <flux:badge size="xs" :color="$record->is_video_recorded ? 'emerald' : 'red'">Video</flux:badge>
                                            <flux:badge size="xs" :color="$record->accessories_checked ? 'emerald' : 'red'">Accessories</flux:badge>
                                            @if($record->is_insured)
                                                <flux:badge size="xs" color="emerald">Insured</flux:badge>
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

    {{-- Video upload & maintenance logs --}}
    <div class="border-t border-zinc-200 dark:border-zinc-700 p-4 md:p-6 space-y-6">
        <div>
            <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Service video</h4>
            <input type="file" wire:model="videoFile" accept="video/*" class="text-sm" />
            @error('videoFile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <flux:button size="sm" class="mt-2" wire:click="uploadVideo" wire:loading.attr="disabled">Upload video</flux:button>
            @if($videos->isNotEmpty())
                <ul class="mt-3 text-xs text-zinc-500 space-y-1">
                    @foreach($videos as $video)
                        <li wire:key="vid-{{ $video->id }}">{{ basename($video->video_path) }} — {{ $video->recorded_at?->format('d M Y H:i') }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if($activeItem)
            <div>
                <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Maintenance log</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <input wire:model="logDescription" type="text" placeholder="Description" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                    <input wire:model="logCost" type="number" step="0.01" min="0" placeholder="Cost (£)" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                    <input wire:model="logServicedAt" type="date" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                    <input wire:model="logNote" type="text" placeholder="Note (optional)" class="border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                </div>
                <flux:button size="sm" wire:click="addMaintenanceLog">Save maintenance log</flux:button>
                @if($maintenanceLogs->isNotEmpty())
                    <ul class="mt-3 text-xs text-zinc-500 space-y-1">
                        @foreach($maintenanceLogs as $log)
                            <li wire:key="ml-{{ $log->id }}">{{ $log->serviced_at?->format('d M Y') }} — {{ $log->description }} (£{{ number_format((float) $log->cost, 2) }})</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>
</div>
