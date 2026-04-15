<div>
    <x-flux-admin::summary-header
        :title="$clubMember->full_name"
        :subtitle="$clubMember->email"
        :backUrl="route('flux-admin.club.index')"
        backLabel="Back to Club Members"
        :badges="array_filter([
            ['label' => $clubMember->is_active ? 'Active' : 'Inactive', 'color' => $clubMember->is_active ? 'green' : 'zinc'],
            $clubMember->is_partner ? ['label' => 'Partner: ' . ($clubMember->partner?->companyname ?? '—'), 'color' => 'blue'] : null,
        ])"
    >
        <x-slot:stats>
            <x-flux-admin::stat-card label="Total Purchases" :value="'£' . number_format($clubMember->total_purchases, 2)" icon="shopping-cart" colour="blue" />
            <x-flux-admin::stat-card label="Total Discounts" :value="'£' . number_format($clubMember->total_discounts, 2)" icon="receipt-percent" colour="green" />
            <x-flux-admin::stat-card label="Redeemable Balance" :value="'£' . number_format($clubMember->available_redeemable_balance, 2)" icon="banknotes" colour="purple" />
            <x-flux-admin::stat-card label="Total Spending" :value="'£' . number_format($clubMember->total_spending, 2)" icon="credit-card" colour="amber" />
            <x-flux-admin::stat-card label="Unpaid Spending" :value="'£' . number_format($clubMember->total_unpaid_spending, 2)" icon="exclamation-triangle" colour="red" />
        </x-slot:stats>
    </x-flux-admin::summary-header>

    {{-- Tabs --}}
    <div class="mb-6">
        <flux:tabs wire:model.live="activeTab">
            <flux:tab name="overview">Overview</flux:tab>
            <flux:tab name="purchases">Purchases</flux:tab>
            <flux:tab name="redemptions">Redemptions</flux:tab>
            <flux:tab name="spendings">Spendings</flux:tab>
            <flux:tab name="activity">Activity</flux:tab>
        </flux:tabs>
    </div>

    {{-- Tab content --}}
    @switch($activeTab)
        @case('overview')
            <livewire:flux-admin.partials.club.overview-tab :clubMemberId="$clubMember->id" key="tab-overview-{{ $clubMember->id }}" />
            @break
        @case('purchases')
            <livewire:flux-admin.partials.club.purchases-tab :clubMemberId="$clubMember->id" key="tab-purchases-{{ $clubMember->id }}" />
            @break
        @case('redemptions')
            <livewire:flux-admin.partials.club.redemptions-tab :clubMemberId="$clubMember->id" key="tab-redemptions-{{ $clubMember->id }}" />
            @break
        @case('spendings')
            <livewire:flux-admin.partials.club.spendings-tab :clubMemberId="$clubMember->id" key="tab-spendings-{{ $clubMember->id }}" />
            @break
        @case('activity')
            <livewire:flux-admin.partials.club.activity-tab :clubMemberId="$clubMember->id" key="tab-activity-{{ $clubMember->id }}" />
            @break
    @endswitch
</div>
