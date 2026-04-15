<div>
    <x-flux-admin::summary-header
        :title="$branch->name"
        :subtitle="$branch->address . ', ' . $branch->city"
        :backUrl="route('flux-admin.branches.index')"
        backLabel="Back to Branches"
    />

    <div class="mb-6">
        <flux:tabs wire:model.live="activeTab">
            <flux:tab name="motorbikes">Motorbikes</flux:tab>
            <flux:tab name="info">Info</flux:tab>
        </flux:tabs>
    </div>

    @switch($activeTab)
        @case('motorbikes')
            <livewire:flux-admin.partials.branches.motorbikes-tab :branchId="$branch->id" key="tab-motorbikes-{{ $branch->id }}" />
            @break
        @case('info')
            <livewire:flux-admin.partials.branches.info-tab :branchId="$branch->id" key="tab-info-{{ $branch->id }}" />
            @break
    @endswitch
</div>
