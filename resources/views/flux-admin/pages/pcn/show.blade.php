<div>
    <x-flux-admin::summary-header
        :title="'PCN ' . $pcnCase->pcn_number"
        :subtitle="trim(($pcnCase->customer?->first_name ?? '') . ' ' . ($pcnCase->customer?->last_name ?? '')) . ($pcnCase->motorbike ? ' · ' . $pcnCase->motorbike->reg_no : '')"
        :backUrl="route('flux-admin.pcn.index')"
        backLabel="Back to PCN Cases"
        :badges="array_filter([
            ['label' => $pcnCase->isClosed ? 'Closed' : 'Open', 'color' => $pcnCase->isClosed ? 'zinc' : 'green'],
            $pcnCase->is_police ? ['label' => 'Police', 'color' => 'red'] : null,
        ])"
    >
        <x-slot:stats>
            <x-flux-admin::stat-card label="Full Amount" :value="'£' . number_format($pcnCase->full_amount ?? 0, 2)" icon="banknotes" colour="red" />
            <x-flux-admin::stat-card label="Reduced Amount" :value="'£' . number_format($pcnCase->reduced_amount ?? 0, 2)" icon="receipt-percent" colour="amber" />
            <x-flux-admin::stat-card label="Days Since" :value="$pcnCase->getDaysSinceContravention()" icon="clock" colour="blue" />
            <x-flux-admin::stat-card label="Updates" :value="$pcnCase->updates()->count()" icon="arrow-path" colour="purple" />
        </x-slot:stats>
    </x-flux-admin::summary-header>

    {{-- Tabs --}}
    <div class="mb-6">
        <flux:tabs wire:model.live="activeTab">
            <flux:tab name="details">Details</flux:tab>
            <flux:tab name="updates">Updates</flux:tab>
            <flux:tab name="tol">TOL Requests</flux:tab>
            <flux:tab name="payments">Payments</flux:tab>
        </flux:tabs>
    </div>

    {{-- Tab content --}}
    @switch($activeTab)
        @case('details')
            <livewire:flux-admin.partials.pcn.case-details-tab :pcnCaseId="$pcnCase->id" key="tab-details-{{ $pcnCase->id }}" />
            @break
        @case('updates')
            <livewire:flux-admin.partials.pcn.updates-tab :pcnCaseId="$pcnCase->id" key="tab-updates-{{ $pcnCase->id }}" />
            @break
        @case('tol')
            <livewire:flux-admin.partials.pcn.tol-requests-tab :pcnCaseId="$pcnCase->id" key="tab-tol-{{ $pcnCase->id }}" />
            @break
        @case('payments')
            <livewire:flux-admin.partials.pcn.payments-tab :pcnCaseId="$pcnCase->id" key="tab-payments-{{ $pcnCase->id }}" />
            @break
    @endswitch
</div>
