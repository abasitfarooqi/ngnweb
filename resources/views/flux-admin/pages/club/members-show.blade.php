<div class="space-y-4">
    @php
        $displayTitle = trim((string) $clubMember->full_name);
        if ($displayTitle === '' || $displayTitle === '-') {
            $displayTitle = \App\Support\ClubMemberStaffAccess::formatField($clubMember->vrm) !== '—'
                ? \App\Support\ClubMemberStaffAccess::formatField($clubMember->vrm)
                : ('Member #'.$clubMember->id);
        }
    @endphp

    <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
            <a href="{{ route('flux-admin.club-members.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500 transition hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                <flux:icon name="arrow-left" variant="micro" class="h-3 w-3" />
                Back to Club Members
            </a>
            <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $displayTitle }}</h1>
                    <flux:badge :color="$clubMember->is_active ? 'green' : 'zinc'" size="sm">
                        {{ $clubMember->is_active ? 'Active' : 'Inactive' }}
                    </flux:badge>
                    @if($clubMember->is_partner)
                        <flux:badge color="blue" size="sm">Partner</flux:badge>
                    @endif
                </div>
                <button
                    type="button"
                    class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500 transition hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                    onclick="window.scrollToClubMemberEdit?.()"
                >
                    <flux:icon name="pencil-square" variant="micro" class="h-3 w-3" />
                    Edit vehicle &amp; partner
                    <flux:icon name="arrow-down" variant="micro" class="h-3 w-3" />
                </button>
            </div>
        </div>

        @include('flux-admin.partials.club.members-show-details', ['clubMember' => $clubMember])
    </div>

    <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="grid grid-cols-2 gap-px bg-zinc-200 dark:bg-zinc-700 xl:grid-cols-5">
            <x-flux-admin::stat-card class="!border-0" label="Total Purchases" :value="'£' . number_format($clubMember->total_purchases, 2)" icon="shopping-cart" colour="blue" />
            <x-flux-admin::stat-card class="!border-0" label="Total Discounts" :value="'£' . number_format($clubMember->total_discounts, 2)" icon="receipt-percent" colour="green" />
            <x-flux-admin::stat-card class="!border-0" label="Redeemable Balance" :value="'£' . number_format($clubMember->available_redeemable_balance, 2)" icon="banknotes" colour="purple" />
            <x-flux-admin::stat-card class="!border-0" label="Total Spending" :value="'£' . number_format($clubMember->total_spending, 2)" icon="credit-card" colour="amber" />
            <x-flux-admin::stat-card class="!border-0" label="Unpaid Spending" :value="'£' . number_format($clubMember->total_unpaid_spending, 2)" icon="exclamation-triangle" colour="red" />
        </div>
    </div>

    <section class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-4 pt-3 dark:border-zinc-800">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <flux:tabs wire:model.live="activeTab">
                    <flux:tab name="spendings">Spendings</flux:tab>
                    <flux:tab name="activity">Activity</flux:tab>
                </flux:tabs>
                <div class="min-w-0 w-full pb-3 sm:max-w-xs">
                    <flux:input
                        wire:model.live.debounce.300ms="highlightInvoice"
                        placeholder="Find POS invoice…"
                        variant="filled"
                    />
                </div>
            </div>
            @if($invoiceNotFound)
                <p class="pb-3 text-sm font-medium text-zinc-800 dark:text-zinc-100">
                    POS invoice not found for this member.
                </p>
            @endif
        </div>

        <div class="club-members-tab-body">
            @switch($activeTab)
                @case('spendings')
                    <livewire:flux-admin.partials.club.spendings-tab :clubMemberId="$clubMember->id" :highlightInvoice="$highlightInvoice" :key="'members-tab-spendings-'.$clubMember->id.'-'.$highlightInvoice" />
                    @break
                @case('activity')
                    <livewire:flux-admin.partials.club.activity-tab :clubMemberId="$clubMember->id" :highlightInvoice="$highlightInvoice" :key="'members-tab-activity-'.$clubMember->id.'-'.$highlightInvoice" />
                    @break
            @endswitch
        </div>
    </section>

    <form id="club-member-edit-vehicle" wire:submit.prevent="saveVehicle" class="scroll-mt-6 border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Edit vehicle &amp; partner</h2>
        </div>

        <div class="p-4">
            <div class="flux-admin-form-grid grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-flux-admin::field-group label="VRM" :error="$errors->first('vehicleForm.vrm')">
                    <flux:input wire:model="vehicleForm.vrm" placeholder="Registration plate" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Make" :error="$errors->first('vehicleForm.make')">
                    <flux:input wire:model="vehicleForm.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('vehicleForm.model')">
                    <flux:input wire:model="vehicleForm.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('vehicleForm.year')">
                    <flux:input type="number" wire:model="vehicleForm.year" min="1990" max="2100" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="NGN partner" :error="$errors->first('vehicleForm.ngn_partner_id')" class="sm:col-span-2">
                    <flux:select wire:model="vehicleForm.ngn_partner_id" placeholder="Select partner">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($partners as $partner)
                            <flux:select.option value="{{ $partner->id }}">{{ $partner->companyname }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <label class="mt-3 flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                <input type="checkbox" wire:model="vehicleForm.is_partner" class="accent-zinc-900 dark:accent-zinc-200"> Partner member
            </label>

            <div class="mt-4 flex justify-end">
                <flux:button type="submit" variant="primary" class="!rounded-none">Save vehicle &amp; partner</flux:button>
            </div>
        </div>
    </form>
</div>

@script
<script>
    window.scrollToClubMemberEdit = function () {
        const target = document.getElementById('club-member-edit-vehicle');
        if (!target) {
            return;
        }

        target.scrollIntoView({ behavior: 'smooth', block: 'start' });

        if (window.location.hash !== '#club-member-edit-vehicle') {
            history.replaceState(null, '', '#club-member-edit-vehicle');
        }
    };

    const scrollIfHash = () => {
        if (window.location.hash === '#club-member-edit-vehicle') {
            window.scrollToClubMemberEdit();
        }

        const hit = document.getElementById('club-pos-invoice-hit');
        if (hit) {
            hit.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    scrollIfHash();
    document.addEventListener('livewire:navigated', scrollIfHash);
</script>
@endscript
