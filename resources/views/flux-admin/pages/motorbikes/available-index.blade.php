<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Repair rental availability</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Find bikes missing from New booking select (profile, pricing, registration, MOT/tax, or a stuck open rental) and fix them in one click — same rules as legacy make available.
            </p>
        </div>
        <div class="flex gap-2">
            @if($exportable)
                <x-flux-admin::export-button />
            @endif
            <flux:button href="{{ route('flux-admin.new-booking.index') }}" wire:navigate variant="ghost" class="!rounded-none">New booking</flux:button>
        </div>
    </div>

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search registration, make, model…" variant="filled" />
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                <div class="min-w-0 w-full sm:min-w-[11rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.selectable" placeholder="Selectable">
                        <flux:select.option value="">Any selectability</flux:select.option>
                        <flux:select.option value="0">Blocked (not on New booking)</flux:select.option>
                        <flux:select.option value="1">Selectable on New booking</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[12rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filters.vehicle_profile_id" placeholder="Vehicle profile">
                        <flux:select.option value="">Any profile</flux:select.option>
                        @foreach($profiles as $id => $name)
                            <flux:select.option value="{{ $id }}">{{ $name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.mot_status" placeholder="MOT status">
                        <flux:select.option value="">Any MOT</flux:select.option>
                        <flux:select.option value="Valid">Valid</flux:select.option>
                        <flux:select.option value="No details held by DVLA">No details held by DVLA</flux:select.option>
                        <flux:select.option value="Expired">Expired</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.road_tax_status" placeholder="Road tax">
                        <flux:select.option value="">Any tax</flux:select.option>
                        <flux:select.option value="Taxed">Taxed</flux:select.option>
                        <flux:select.option value="SORN">SORN</flux:select.option>
                        <flux:select.option value="No details held by DVLA">No details held by DVLA</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[8rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="filters.is_posted" placeholder="Open booking">
                        <flux:select.option value="">Any booking</flux:select.option>
                        <flux:select.option value="1">Has open posted</flux:select.option>
                        <flux:select.option value="0">No open posted</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[8rem] sm:flex-1 lg:w-36 lg:flex-none">
                    <flux:select wire:model.live="filters.iscurrent" placeholder="Current price">
                        <flux:select.option value="">Any pricing</flux:select.option>
                        <flux:select.option value="1">Current yes</flux:select.option>
                        <flux:select.option value="0">Current no</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-32">
                    <flux:select wire:model.live="perPage">
                        <flux:select.option value="20">20 per page</flux:select.option>
                        <flux:select.option value="50">50 per page</flux:select.option>
                        <flux:select.option value="100">100 per page</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[78rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'reg_no'" :direction="$sortField === 'reg_no' ? $sortDirection : null" wire:click="sortBy('reg_no')">Reg No</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'vehicle_profile_id'" :direction="$sortField === 'vehicle_profile_id' ? $sortDirection : null" wire:click="sortBy('vehicle_profile_id')">Profile</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'mot_status'" :direction="$sortField === 'mot_status' ? $sortDirection : null" wire:click="sortBy('mot_status')">MOT</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'road_tax_status'" :direction="$sortField === 'road_tax_status' ? $sortDirection : null" wire:click="sortBy('road_tax_status')">Tax</flux:table.column>
                        <flux:table.column>Open booking</flux:table.column>
                        <flux:table.column>Reg row</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'weekly_price'" :direction="$sortField === 'weekly_price' ? $sortDirection : null" wire:click="sortBy('weekly_price')">Weekly £</flux:table.column>
                        <flux:table.column>Current</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($rows as $row)
                            <flux:table.row wire:key="avail-{{ $row->id }}">
                                <flux:table.cell class="font-mono font-medium text-zinc-900 dark:text-white">{{ $row->reg_no }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $row->vehicle_profile_id ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $row->mot_status ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $row->road_tax_status ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                                    @if($row->booking_is_posted)
                                        Open (no end)
                                    @else
                                        —
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $row->has_registration ? 'Yes' : 'Missing' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                                    {{ $row->weekly_price !== null ? '£'.number_format((float) $row->weekly_price, 2) : '—' }}
                                </flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $row->iscurrent ? 'Yes' : 'No' }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <flux:button size="sm" variant="primary" wire:click="openRepair({{ $row->id }})" class="!rounded-none">Repair rental availability fix</flux:button>
                                        <a href="{{ route('flux-admin.backpack.motorbike-available.edit', $row->id) }}" wire:navigate>
                                            <flux:button size="sm" variant="ghost" class="!rounded-none">Edit</flux:button>
                                        </a>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="9" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No motorbikes found.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>

    <flux:modal wire:model.self="showRepairForm" class="md:w-[600px]">
        <div class="space-y-4">
            <flux:heading size="lg">Repair rental availability — {{ $repairRegNo ?: 'Motorbike' }}</flux:heading>

            @if($lastRepairMessage !== '')
                <div class="border border-zinc-300 bg-zinc-50 dark:bg-zinc-950 dark:border-zinc-700 p-3 text-sm text-zinc-800 dark:text-zinc-200">
                    {{ $lastRepairMessage }}
                </div>
            @endif

            @if(!empty($repairChecks))
                <div class="border border-zinc-200 dark:border-zinc-800 p-3 text-sm space-y-1">
                    <p class="font-medium text-zinc-900 dark:text-white">New booking eligibility checks</p>
                    <p>Profile internal (1): <span class="{{ !empty($repairChecks['vehicle_profile_ok']) ? 'text-green-600' : 'text-red-600' }}">{{ !empty($repairChecks['vehicle_profile_ok']) ? 'Yes' : 'No' }}</span></p>
                    <p>Current pricing: <span class="{{ !empty($repairChecks['has_current_pricing']) ? 'text-green-600' : 'text-red-600' }}">{{ !empty($repairChecks['has_current_pricing']) ? 'Yes' : 'Missing' }}</span></p>
                    <p>Registration row: <span class="{{ !empty($repairChecks['has_registration']) ? 'text-green-600' : 'text-red-600' }}">{{ !empty($repairChecks['has_registration']) ? 'Yes' : 'Missing' }}</span></p>
                    <p>MOT/tax pass: <span class="{{ !empty($repairChecks['compliance_pass']) ? 'text-green-600' : 'text-red-600' }}">{{ !empty($repairChecks['compliance_pass']) ? 'Yes' : 'No' }}</span>
                        <span class="text-zinc-500">({{ $repairChecks['mot_status'] ?? '—' }} / {{ $repairChecks['road_tax_status'] ?? '—' }})</span>
                    </p>
                    <p>Open posted rental: <span class="{{ empty($repairChecks['has_open_posted_item']) ? 'text-green-600' : 'text-red-600' }}">{{ !empty($repairChecks['has_open_posted_item']) ? 'Yes — will force-close' : 'No' }}</span></p>
                    @if(!empty($repairChecks['blockers']))
                        <ul class="mt-2 list-disc pl-5 text-red-700 dark:text-red-400">
                            @foreach($repairChecks['blockers'] as $blocker)
                                <li>{{ $blocker }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @if(!empty($repairPreview) && ($repairPreview['open_posted_items_count'] ?? 0) > 0)
                <div class="border border-amber-300 bg-amber-50 dark:bg-amber-950 dark:border-amber-700 p-3 text-sm">
                    <p class="font-medium text-amber-800 dark:text-amber-300">{{ $repairPreview['open_posted_items_count'] }} open posted rental item(s) will be force-closed:</p>
                    <ul class="mt-2 space-y-1 text-amber-700 dark:text-amber-400">
                        @foreach($repairPreview['items'] as $item)
                            <li>· Item #{{ $item['item_id'] }} · Booking #{{ $item['booking_id'] }} · {{ $item['booking_state'] }} · started {{ $item['start_date'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                One click will: set profile to internal, create missing registration, ensure current pricing (fallback £70), set MOT/tax to selectable values if needed, and force-end stuck open rentals.
            </p>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showRepairForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="button" variant="primary" wire:click="executeRepair" wire:loading.attr="disabled" class="!rounded-none">
                    Confirm — repair rental availability fix
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
