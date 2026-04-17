<div>
    <x-flux-admin::summary-header
        :title="'Finance #' . $application->id"
        :subtitle="$application->customer ? $application->customer->first_name . ' ' . $application->customer->last_name : 'No customer'"
        :badges="array_filter([
            ['label' => $this->getContractType(), 'color' => 'zinc'],
            $application->is_cancelled ? ['label' => 'Cancelled', 'color' => 'red'] : null,
            $application->is_monthly ? ['label' => 'Monthly', 'color' => 'blue'] : null,
        ])"
        :backUrl="route('flux-admin.finance.index')"
        backLabel="All Applications"
    >
        <x-slot:stats>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Deposit</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">£{{ number_format($application->deposit ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Weekly Instalment</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">£{{ number_format($application->weekly_instalment ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Motorbike Price</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">£{{ number_format($application->motorbike_price ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Items</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $application->items->count() }}</p>
            </div>
        </x-slot:stats>
    </x-flux-admin::summary-header>

    {{-- Tabs --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 mb-6">
        <div class="border-b border-zinc-200 dark:border-zinc-700 overflow-x-auto">
            <nav class="flex -mb-px">
                @foreach(['contract' => 'Contract Details', 'items' => 'Items', 'extras' => 'Extras', 'instalments' => 'Instalments', 'signing' => 'Signing Access', 'documents' => 'Documents'] as $tab => $label)
                    <button
                        wire:click="$set('activeTab', '{{ $tab }}')"
                        class="px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $activeTab === $tab ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-5">
            @switch($activeTab)
                @case('contract')
                    <livewire:flux-admin.partials.finance.contract-details-tab :applicationId="$application->id" wire:key="tab-contract-{{ $application->id }}" />
                    @break
                @case('items')
                    <livewire:flux-admin.partials.finance.application-items-tab :applicationId="$application->id" wire:key="tab-items-{{ $application->id }}" />
                    @break
                @case('extras')
                    <livewire:flux-admin.partials.finance.extra-items-tab :applicationId="$application->id" wire:key="tab-extras-{{ $application->id }}" />
                    @break
                @case('instalments')
                    <livewire:flux-admin.partials.finance.instalment-tracker-tab :applicationId="$application->id" wire:key="tab-instalments-{{ $application->id }}" />
                    @break
                @case('signing')
                    <livewire:flux-admin.partials.finance.signing-access-tab :applicationId="$application->id" wire:key="tab-signing-{{ $application->id }}" />
                    @break
                @case('documents')
                    <livewire:flux-admin.partials.finance.documents-tab :applicationId="$application->id" wire:key="tab-documents-{{ $application->id }}" />
                    @break
            @endswitch
        </div>
    </div>
</div>
