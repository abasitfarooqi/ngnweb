<div>
    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.finance.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Finance Applications</a>
                <span>/</span>
                <span>{{ $application && $application->exists ? 'Edit #' . $application->id : 'New Application' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $application && $application->exists ? 'Edit Finance Application #' . $application->id : 'New Finance Application' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.finance.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">
                {{ $application && $application->exists ? 'Update application' : 'Create application' }}
            </flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Customer</h2>

            <div>
                <flux:label>Customer <span class="text-red-500">*</span></flux:label>
                @if($application && $application->exists && $customerSearch)
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Current: <strong>{{ $customerSearch }}</strong></p>
                @endif
                <div class="relative">
                    <flux:input wire:model.live.debounce.300ms="customerSearch"
                                placeholder="Search by name, email or phone…"
                                autocomplete="off" />
                    @if(count($customerSuggestions) > 0)
                        <ul class="absolute z-50 mt-1 w-full border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900 max-h-56 overflow-y-auto">
                            @foreach($customerSuggestions as $s)
                                <li wire:click="selectCustomer({{ $s['id'] }}, '{{ addslashes($s['name']) }}')"
                                    class="cursor-pointer px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                    <span class="font-medium text-sm text-zinc-900 dark:text-white">{{ $s['name'] }}</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $s['sub'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @error('form.customer_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Contract details</h2>

            {{-- Contract type --}}
            <div class="mb-5">
                <flux:label>Contract Type</flux:label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1">
                    @foreach([
                        'is_new_latest'   => 'New Latest Contract',
                        'is_used_latest'  => 'Used Latest Contract',
                        'is_subscription' => '12 Months Subscription',
                    ] as $key => $label)
                        <label class="flex items-center gap-2 cursor-pointer border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm {{ !empty($form[$key]) ? 'bg-blue-50 border-blue-400 dark:bg-blue-950 dark:border-blue-500' : 'bg-white dark:bg-zinc-900' }}">
                            <input type="radio" wire:click="setContractType('{{ $key }}')" {{ !empty($form[$key]) ? 'checked' : '' }} class="text-blue-600" />
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Contract Date" :error="$errors->first('form.contract_date')">
                    <flux:input type="date" wire:model="form.contract_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="First Instalment Date" :error="$errors->first('form.first_instalment_date')">
                    <flux:input type="date" wire:model="form.first_instalment_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Motorbike Price (£)" :error="$errors->first('form.motorbike_price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.motorbike_price" placeholder="0.00" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Monthly Instalment (£)" :error="$errors->first('form.weekly_instalment')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.weekly_instalment" placeholder="0.00" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Deposit (£)" :error="$errors->first('form.deposit')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.deposit" placeholder="0.00" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Extra Amount (£)" :error="$errors->first('form.extra')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.extra" placeholder="0.00" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Extra Items" :error="$errors->first('form.extra_items')">
                    <flux:textarea wire:model="form.extra_items" placeholder="Itemise all additional products or services…" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Notes (internal only)" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" placeholder="Internal notes, not visible to customer…" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <flux:checkbox wire:model="form.is_monthly" />
                    Monthly billing
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <flux:checkbox wire:model="form.is_posted" />
                    Posted
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <flux:checkbox wire:model="form.log_book_sent" />
                    Log book sent
                </label>
            </div>
        </div>

        @if($application && $application->exists)
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Cancellation</h2>

            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <flux:checkbox wire:model="form.is_cancelled" />
                Mark as cancelled
            </label>

            @if(!empty($form['is_cancelled']))
            <div class="mt-3">
                <x-flux-admin::field-group label="Reason of cancellation" :error="$errors->first('form.reason_of_cancellation')">
                    <flux:textarea wire:model="form.reason_of_cancellation" rows="2" />
                </x-flux-admin::field-group>
            </div>
            @endif
        </div>
        @endif

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.finance.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">
                {{ $application && $application->exists ? 'Update application' : 'Create application' }}
            </flux:button>
        </div>

    </form>
</div>
