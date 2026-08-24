<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ \App\Support\FluxAdminFinanceListQuery::indexUrl() }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200">Payment Plan Applications</a>
                <span>/</span>
                <span>{{ $application && $application->exists ? 'Edit #' . $application->id : 'New Application' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $application && $application->exists ? 'Edit Finance Application #' . $application->id : 'New Finance Application' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ \App\Support\FluxAdminFinanceListQuery::indexUrl() }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">
                {{ $application && $application->exists ? 'Update application' : 'Create application' }}
            </flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>

        <div class="flux-admin-panel border border-zinc-200 p-5 dark:border-zinc-800">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Customer</h2>
            <div>
                <flux:label>Customer <span class="text-red-500">*</span></flux:label>
                @if($application && $application->exists && $customerSearch)
                    <p class="mb-1 text-sm text-zinc-600 dark:text-zinc-400">Current: <strong>{{ $customerSearch }}</strong></p>
                @endif
                <div class="max-w-xl {{ count($customerSuggestions) > 0 ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                    <flux:input wire:model.live.debounce.300ms="customerSearch"
                                placeholder="Search by name, email or phone…"
                                autocomplete="off" />
                    @if(count($customerSuggestions) > 0)
                        <ul class="flux-admin-autocomplete-menu" role="listbox">
                            @foreach($customerSuggestions as $s)
                                <li role="option" wire:mousedown.prevent="selectCustomer({{ $s['id'] }}, @js($s['name']))">
                                    <span class="text-sm font-medium">{{ $s['name'] }}</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $s['sub'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @error('form.customer_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flux-admin-panel border border-zinc-200 p-5 dark:border-zinc-800 {{ collect($motorbikeSuggestions ?? [])->filter()->isNotEmpty() ? 'relative z-20 overflow-visible' : 'overflow-visible' }}">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Contract details</h2>

            <div class="mb-5">
                <flux:label>Contract Type <span class="text-red-500">*</span></flux:label>
                <div class="mt-1 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach([
                        'is_new_latest'  => 'New Latest (New sale contract)',
                        'is_used_latest' => 'Used Latest (Used sale contract)',
                    ] as $key => $label)
                        <label @class([
                            'flux-admin-choice flex cursor-pointer items-center gap-2 border px-3 py-2 text-sm',
                            'flux-admin-choice-active border-blue-400 dark:border-blue-500' => ($form['contract_type'] ?? '') === $key,
                            'border-zinc-200 dark:border-zinc-700' => ($form['contract_type'] ?? '') !== $key,
                        ])>
                            <input type="radio"
                                   name="contract_type"
                                   value="{{ $key }}"
                                   wire:model.live="form.contract_type"
                                   class="text-blue-600" />
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('form.contract_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- No light “bar”: plain options row works in light and dark --}}
            <div class="mb-5 flex flex-wrap items-center gap-x-5 gap-y-2">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-800 dark:text-zinc-200">
                    <flux:checkbox wire:model.live="form.is_subscription" />
                    12 Months Subscription
                </label>
                <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    <flux:checkbox wire:model="form.no_email" />
                    No email
                </label>
                <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    <flux:checkbox wire:model="form.is_posted" />
                    Generate Contract
                </label>
                <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <flux:checkbox wire:model="form.insurance_pcn" />
                    Insurance / PCN
                </label>
                @if($application && $application->exists)
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-800 dark:text-zinc-200">
                        <flux:checkbox wire:model.live="form.log_book_sent" />
                        Logbook V5C
                    </label>
                @endif
            </div>

            <div class="flux-admin-form-grid grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-flux-admin::field-group label="Contract date &amp; time" :error="$errors->first('form.contract_date')">
                    <flux:input type="datetime-local" wire:model="form.contract_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="First Instalment Date" :error="$errors->first('form.first_instalment_date')">
                    <flux:input type="date" wire:model="form.first_instalment_date" />
                </x-flux-admin::field-group>

                @if($this->shouldShowPaymentDayField())
                    <x-flux-admin::field-group label="Payment day of month" hint="Day customer pays each month (1–31). Not the instalment amount." :error="$errors->first('form.subs_payment_date')">
                        <flux:input type="number" min="1" max="31" wire:model="form.subs_payment_date" placeholder="e.g. 15" />
                    </x-flux-admin::field-group>
                @endif

                <x-flux-admin::field-group label="Monthly Instalment (£)" hint="Monthly fee used on sale and 12-month subscription contracts." :error="$errors->first('form.weekly_instalment')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.weekly_instalment" placeholder="0.00" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Motorbike Price (£)" :error="$errors->first('form.motorbike_price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.motorbike_price" placeholder="0.00" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Deposit (£)" :error="$errors->first('form.deposit')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.deposit" placeholder="0.00" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Extra Amount (£)" :error="$errors->first('form.extra')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.extra" placeholder="0.00" />
                </x-flux-admin::field-group>

                @if($application && $application->exists && !empty($form['log_book_sent']))
                    <x-flux-admin::field-group label="Logbook transfer date" :error="$errors->first('form.logbook_transfer_date')">
                        <flux:input type="date" wire:model="form.logbook_transfer_date" />
                    </x-flux-admin::field-group>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <x-flux-admin::field-group label="Extra Items" :error="$errors->first('form.extra_items')">
                    <flux:textarea wire:model="form.extra_items" placeholder="Itemise additional products or services…" rows="3" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Notes (internal only)" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" placeholder="Internal notes…" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 border-t border-zinc-200 pt-4 dark:border-zinc-800 overflow-visible">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Application Items</h3>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">At least one motorbike is required.</p>
                    </div>
                    <flux:button type="button" size="xs" variant="ghost" icon="plus" wire:click="addItemRow" class="!rounded-none">Add item</flux:button>
                </div>

                <div class="space-y-3 overflow-visible">
                    @foreach($itemRows as $index => $item)
                        <div class="grid grid-cols-1 gap-3 border border-zinc-200 p-3 dark:border-zinc-800 sm:grid-cols-[minmax(0,1fr)_auto_auto] {{ !empty($motorbikeSuggestions[$index]) ? 'relative z-30' : 'relative z-0' }}" wire:key="application-item-row-{{ $index }}">
                            <div>
                                <flux:label>Motorbike <span class="text-red-500">*</span></flux:label>
                                <div class="{{ !empty($motorbikeSuggestions[$index]) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                                    <flux:input
                                        wire:model.live.debounce.100ms="motorbikeSearches.{{ $index }}"
                                        x-on:keydown.enter.prevent="$wire.commitMotorbikeSearch({{ $index }})"
                                        placeholder="Search by reg, make, model or VIN..."
                                        autocomplete="off"
                                    />
                                    @if(!empty($motorbikeSuggestions[$index]))
                                        <ul class="flux-admin-autocomplete-menu" role="listbox" wire:key="motorbike-suggestions-{{ $index }}-{{ count($motorbikeSuggestions[$index]) }}">
                                            @foreach($motorbikeSuggestions[$index] as $suggestion)
                                                <li role="option" wire:mousedown.prevent="selectMotorbike({{ $index }}, {{ $suggestion['id'] }}, @js($suggestion['label']))">
                                                    {{ $suggestion['label'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if(!empty($item['motorbike_id']))
                                        <p class="mt-1 text-[11px] text-emerald-700 dark:text-emerald-400">Selected bike #{{ $item['motorbike_id'] }}</p>
                                    @endif
                                </div>
                                @error("itemRows.$index.motorbike_id") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                @error('itemRows') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <label class="flex items-center gap-2 text-sm sm:self-end sm:pb-2">
                                <flux:checkbox wire:model="itemRows.{{ $index }}.is_posted" />
                                Item posted
                            </label>

                            <div class="sm:self-end">
                                <flux:button type="button" size="xs" variant="ghost" icon="trash" wire:click="removeItemRow({{ $index }})" class="!rounded-none text-red-600" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($application && $application->exists)
        <div class="flux-admin-panel border border-zinc-200 p-5 dark:border-zinc-800">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Cancellation</h2>

            <label class="flex cursor-pointer items-center gap-2 text-sm">
                <flux:checkbox wire:model="form.is_cancelled" />
                Mark as cancelled
            </label>

            @if(!empty($form['is_cancelled']))
            <div class="mt-3 max-w-xl">
                <x-flux-admin::field-group label="Reason of cancellation" :error="$errors->first('form.reason_of_cancellation')">
                    <flux:textarea wire:model="form.reason_of_cancellation" rows="2" />
                </x-flux-admin::field-group>
            </div>
            @endif
        </div>
        @endif

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ \App\Support\FluxAdminFinanceListQuery::indexUrl() }}" wire:navigate>
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">
                {{ $application && $application->exists ? 'Update application' : 'Create application' }}
            </flux:button>
        </div>

    </form>
</div>
