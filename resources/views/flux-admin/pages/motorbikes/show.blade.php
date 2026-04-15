<div>
    <x-flux-admin::summary-header
        :title="$motorbike->reg_no"
        :subtitle="$motorbike->make . ' ' . $motorbike->model . ' ' . $motorbike->year"
        :backUrl="route('flux-admin.motorbikes.index')"
        backLabel="Back to Motorbikes"
        :badges="array_filter([
            ['label' => $motorbike->branch?->name ?? 'No Branch', 'color' => 'zinc'],
            ['label' => $motorbike->vehicleProfile?->name ?? 'No Profile', 'color' => 'blue'],
            $motorbike->is_ebike ? ['label' => 'E-Bike', 'color' => 'green'] : null,
        ])"
    />

    {{-- Tabs --}}
    <div class="mb-6">
        <flux:tabs wire:model.live="activeTab">
            <flux:tab name="details">Details</flux:tab>
            <flux:tab name="compliance">Compliance</flux:tab>
            <flux:tab name="repairs">Repairs</flux:tab>
            <flux:tab name="registrations">Registrations</flux:tab>
            <flux:tab name="maintenance">Maintenance</flux:tab>
            <flux:tab name="pricing">Pricing</flux:tab>
            <flux:tab name="images">Images</flux:tab>
            <flux:tab name="linked">Linked Records</flux:tab>
        </flux:tabs>
    </div>

    {{-- Tab content --}}
    @switch($activeTab)
        @case('details')
            <livewire:flux-admin.partials.motorbikes.details-tab :motorbikeId="$motorbike->id" key="tab-details-{{ $motorbike->id }}" />
            @break
        @case('compliance')
            <livewire:flux-admin.partials.motorbikes.compliance-tab :motorbikeId="$motorbike->id" key="tab-compliance-{{ $motorbike->id }}" />
            @break
        @case('repairs')
            <livewire:flux-admin.partials.motorbikes.repairs-tab :motorbikeId="$motorbike->id" key="tab-repairs-{{ $motorbike->id }}" />
            @break
        @case('registrations')
            <livewire:flux-admin.partials.motorbikes.registrations-tab :motorbikeId="$motorbike->id" key="tab-registrations-{{ $motorbike->id }}" />
            @break
        @case('maintenance')
            <livewire:flux-admin.partials.motorbikes.maintenance-tab :motorbikeId="$motorbike->id" key="tab-maintenance-{{ $motorbike->id }}" />
            @break
        @case('pricing')
            <livewire:flux-admin.partials.motorbikes.pricing-tab :motorbikeId="$motorbike->id" key="tab-pricing-{{ $motorbike->id }}" />
            @break
        @case('images')
            <livewire:flux-admin.partials.motorbikes.images-tab :motorbikeId="$motorbike->id" key="tab-images-{{ $motorbike->id }}" />
            @break
        @case('linked')
            <livewire:flux-admin.partials.motorbikes.linked-records-tab :motorbikeId="$motorbike->id" key="tab-linked-{{ $motorbike->id }}" />
            @break
    @endswitch
</div>
