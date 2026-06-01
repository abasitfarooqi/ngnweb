<div>
    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.pcn.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">PCN Cases</a>
                <span>/</span>
                <a href="{{ route('flux-admin.pcn.show', $pcnCase) }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">{{ $pcnCase->pcn_number }}</a>
                <span>/</span>
                <span>Edit</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Edit PCN {{ $pcnCase->pcn_number }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.pcn.show', $pcnCase) }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save changes</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>

        {{-- Main fields --}}
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Case details</h2>

            {{-- Customer search --}}
            <div class="mb-4">
                <x-flux-admin::field-group label="Customer" :error="$errors->first('form.customer_id')">
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="customerSearch" placeholder="Search by name or email…" autocomplete="off" />
                        @if(count($customerSuggestions))
                            <ul class="absolute z-50 mt-0.5 w-full border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-lg max-h-52 overflow-y-auto">
                                @foreach($customerSuggestions as $s)
                                    <li wire:click="selectCustomer({{ $s['id'] }}, '{{ addslashes($s['name']) }}')"
                                        class="cursor-pointer px-3 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ $s['name'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <x-flux-admin::field-group label="PCN number" required :error="$errors->first('form.pcn_number')">
                    <flux:input wire:model="form.pcn_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date of contravention" :error="$errors->first('form.date_of_contravention')">
                    <flux:input type="date" wire:model="form.date_of_contravention" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Time" :error="$errors->first('form.time_of_contravention')">
                    <flux:input type="time" wire:model="form.time_of_contravention" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date of letter issued" :error="$errors->first('form.date_of_letter_issued')">
                    <flux:input type="date" wire:model="form.date_of_letter_issued" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Full amount (£)" :error="$errors->first('form.full_amount')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.full_amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Reduced amount (£)" :error="$errors->first('form.reduced_amount')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.reduced_amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike (reg)" :error="$errors->first('form.motorbike_id')">
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="motorbikeSearch" placeholder="Search by reg…" autocomplete="off" />
                        @if(count($motorbikeSuggestions))
                            <ul class="absolute z-50 mt-0.5 w-full border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-lg max-h-44 overflow-y-auto">
                                @foreach($motorbikeSuggestions as $ms)
                                    <li wire:click="selectMotorbike({{ $ms['id'] }}, '{{ addslashes($ms['reg']) }}')"
                                        class="cursor-pointer px-3 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">{{ $ms['reg'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Payment link (council)" :error="$errors->first('form.council_link')">
                    <flux:input type="url" wire:model="form.council_link" placeholder="https://…" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Note" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_police" class="accent-zinc-900 dark:accent-zinc-200"> Police notice
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.isClosed" class="accent-zinc-900 dark:accent-zinc-200"> Mark as closed
                </label>
            </div>
        </div>

        {{-- Repeatable Case Updates --}}
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between px-5 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Case Updates</h2>
                <flux:button type="button" size="xs" variant="ghost" wire:click="addCaseUpdate" icon="plus" class="!rounded-none">Add update</flux:button>
            </div>

            @if(count($caseUpdates))
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($caseUpdates as $idx => $upd)
                        <div class="p-5 space-y-3" wire:key="upd-{{ $idx }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">
                                    Update #{{ $idx + 1 }}
                                    @if(!empty($upd['id'])) <span class="font-normal normal-case">(ID {{ $upd['id'] }})</span>@endif
                                </span>
                                <button type="button" wire:click="removeCaseUpdate({{ $idx }})"
                                    class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">Remove</button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Date</label>
                                    <flux:input type="date" wire:model="caseUpdates.{{ $idx }}.update_date" />
                                </div>
                                <div>
                                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Additional fee (£)</label>
                                    <flux:input type="number" step="0.01" wire:model="caseUpdates.{{ $idx }}.additional_fee" />
                                </div>
                                <div>
                                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Notes</label>
                                    <flux:input wire:model="caseUpdates.{{ $idx }}.note" />
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-5">
                                <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_appealed" class="accent-zinc-900"> Appealed
                                </label>
                                <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_paid_by_owner" class="accent-zinc-900"> Paid by NGN
                                </label>
                                <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_paid_by_keeper" class="accent-zinc-900"> Paid by Hirer
                                </label>
                                <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_transferred" class="accent-zinc-900"> Transferred
                                </label>
                                <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_cancled" class="accent-zinc-900"> Cancelled
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="px-5 py-4 text-sm text-zinc-500 dark:text-zinc-400">No case updates yet. Click "Add update" to add one.</p>
            @endif
        </div>

        {{-- Save bar at bottom --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.pcn.show', $pcnCase) }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save changes</flux:button>
        </div>

    </form>
</div>
