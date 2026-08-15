<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Motorbikes</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $motorbikes->total() }} motorbikes in total</p>
        </div>
        <div class="flex items-center gap-2">
            @if($exportable)
                <x-flux-admin::export-button />
            @endif
            <a href="{{ route('flux-admin.motorbikes.create') }}" wire:navigate>
                <flux:button variant="primary" icon="plus" class="!rounded-none">New motorbike</flux:button>
            </a>
        </div>
    </div>

    @if($deleteError)
        <flux:callout variant="danger" icon="exclamation-triangle" class="mb-4">
            <flux:callout.heading>Delete failed</flux:callout.heading>
            <flux:callout.text class="whitespace-pre-line">{{ $deleteError }}</flux:callout.text>
            <div class="mt-3">
                <flux:button size="sm" variant="ghost" wire:click="dismissDeleteError" class="!rounded-none">Dismiss</flux:button>
            </div>
        </flux:callout>
    @endif

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            {{-- Search --}}
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search registration, make, model, VIN…" variant="filled" />
            </div>

            {{-- Row 2: selects + inputs --}}
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                <div class="min-w-0 w-full sm:min-w-[12rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="branch" placeholder="All branches">
                        <flux:select.option value="">All branches</flux:select.option>
                        @foreach($branches as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="filterType" placeholder="All types">
                        <flux:select.option value="">All types</flux:select.option>
                        <flux:select.option value="1">Internal</flux:select.option>
                        <flux:select.option value="2">External</flux:select.option>
                    </flux:select>
                </div>

                <div class="min-w-0 w-full sm:min-w-[9rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="filterMotStatus" placeholder="MOT status">
                        <flux:select.option value="">Any MOT status</flux:select.option>
                        <flux:select.option value="1">Has compliance record</flux:select.option>
                        <flux:select.option value="0">No compliance record</flux:select.option>
                    </flux:select>
                </div>

                <div class="min-w-0 w-full sm:min-w-[8rem] sm:flex-1 lg:w-32 lg:flex-none">
                    <flux:input wire:model.live.debounce.300ms="filterColour" placeholder="Colour…" variant="filled" />
                </div>

                <div class="min-w-0 w-full sm:min-w-[7rem] sm:flex-1 lg:w-28 lg:flex-none">
                    <flux:input wire:model.live.debounce.300ms="filterYear" placeholder="Year e.g. 2021" variant="filled" type="number" min="1900" max="2100" />
                </div>

                <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-32">
                    <flux:select wire:model.live="perPage">
                        <flux:select.option value="20">20 per page</flux:select.option>
                        <flux:select.option value="50">50 per page</flux:select.option>
                        <flux:select.option value="100">100 per page</flux:select.option>
                    </flux:select>
                </div>
                <flux:button wire:click="resetMotorbikeFilters" variant="ghost" size="sm" icon="x-mark" class="!rounded-none w-full sm:w-auto">
                    Reset filters
                </flux:button>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[72rem] md:min-w-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'reg_no'" :direction="$sortField === 'reg_no' ? $sortDirection : null" wire:click="sortBy('reg_no')">Reg No</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'make'" :direction="$sortField === 'make' ? $sortDirection : null" wire:click="sortBy('make')">Make</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'model'" :direction="$sortField === 'model' ? $sortDirection : null" wire:click="sortBy('model')">Model</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'color'" :direction="$sortField === 'color' ? $sortDirection : null" wire:click="sortBy('color')">Colour</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'year'" :direction="$sortField === 'year' ? $sortDirection : null" wire:click="sortBy('year')">Year</flux:table.column>
                <flux:table.column>Engine</flux:table.column>
                <flux:table.column>MOT Due</flux:table.column>
                <flux:table.column>Road Tax Due</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Profile</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($motorbikes as $row)
                    @php
                        $compliance = $row->latestCompliance;
                        $motDue = $compliance?->mot_due_date ? \Carbon\Carbon::parse($compliance->mot_due_date) : null;
                        $taxDue = $compliance?->tax_due_date ? \Carbon\Carbon::parse($compliance->tax_due_date) : null;
                        $motSoon = $motDue && $motDue->diffInDays(now(), false) >= -30 && $motDue->isFuture();
                        $taxSoon = $taxDue && $taxDue->diffInDays(now(), false) >= -30 && $taxDue->isFuture();
                        $motExpired = $motDue && $motDue->isPast();
                        $taxExpired = $taxDue && $taxDue->isPast();
                    @endphp
                    <flux:table.row wire:key="bike-{{ $row->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.motorbikes.show', $row->id) }}" class="font-medium text-zinc-900 dark:text-white hover:underline">
                                {{ $row->reg_no }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->make }}</flux:table.cell>
                        <flux:table.cell>{{ $row->model }}</flux:table.cell>
                        <flux:table.cell>{{ $row->color ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $row->year }}</flux:table.cell>
                        <flux:table.cell>{{ $row->engine }}</flux:table.cell>
                        <flux:table.cell>
                            @if($motDue)
                                <span @class([
                                    'text-red-600 dark:text-red-400 font-medium' => $motExpired,
                                    'text-amber-600 dark:text-amber-400 font-medium' => $motSoon && !$motExpired,
                                    'text-zinc-700 dark:text-zinc-300' => !$motSoon && !$motExpired,
                                ])>{{ $motDue->format('d M Y') }}</span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-600">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($taxDue)
                                <span @class([
                                    'text-red-600 dark:text-red-400 font-medium' => $taxExpired,
                                    'text-amber-600 dark:text-amber-400 font-medium' => $taxSoon && !$taxExpired,
                                    'text-zinc-700 dark:text-zinc-300' => !$taxSoon && !$taxExpired,
                                ])>{{ $taxDue->format('d M Y') }}</span>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-600">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->branch?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($row->vehicleProfile)
                                <flux:badge color="{{ $row->vehicleProfile->is_internal ? 'zinc' : 'blue' }}" size="sm">
                                    {{ $row->vehicleProfile->name }}
                                </flux:badge>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.motorbikes.edit', $row->id) }}" wire:navigate>
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" icon="check-circle" wire:click="openMakeAvailable({{ $row->id }})" class="!rounded-none text-green-600 dark:text-green-400">Make available</flux:button>
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $row->id }})" wire:confirm="Delete this motorbike?" class="!rounded-none text-red-600 dark:text-red-400">Del</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="11" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No motorbikes found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $motorbikes->links() }}
    </div>

    <flux:modal wire:model.self="showMakeAvailableForm" class="md:w-[540px]">
        <div class="space-y-4">
            <flux:heading size="lg">Make available — {{ $maRegNo ?: 'Motorbike' }}</flux:heading>

            {{-- Preview of open rental links --}}
            @if(!empty($maPreview))
                @if($maPreview['open_items_count'] > 0)
                    <div class="border border-amber-300 bg-amber-50 dark:bg-amber-950 dark:border-amber-700 p-3 text-sm">
                        <p class="font-medium text-amber-800 dark:text-amber-300">{{ $maPreview['open_items_count'] }} open posted rental item(s) will be force-closed:</p>
                        <ul class="mt-2 space-y-1 text-amber-700 dark:text-amber-400">
                            @foreach($maPreview['open_items'] as $item)
                                <li>· Item #{{ $item['item_id'] }} · Booking #{{ $item['booking_id'] }} · {{ $item['booking_state'] }} · started {{ $item['start_date'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="border border-green-300 bg-green-50 dark:bg-green-950 dark:border-green-700 p-3 text-sm text-green-800 dark:text-green-300">
                        No open rental items. Bike is already free of active links.
                    </div>
                @endif
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    Current pricing: <span class="{{ $maPreview['has_current_pricing'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $maPreview['has_current_pricing'] ? 'Set' : 'Missing — update via Motorbike Show page' }}</span>
                </div>
            @endif

            <p class="text-sm text-zinc-600 dark:text-zinc-400">This will end all open posted rental links for this motorbike. The booking items will be closed and their bookings unposted if no other items remain open.</p>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="ghost" wire:click="$set('showMakeAvailableForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button variant="primary" wire:click="executeMakeAvailable" class="!rounded-none">
                    Confirm — make available
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
