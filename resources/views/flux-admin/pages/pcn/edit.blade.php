<div>
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <div class="mb-0.5 flex flex-wrap items-center gap-x-2 gap-y-0 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.pcn.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>PCN Cases</a>
                <span>/</span>
                <a href="{{ route('flux-admin.pcn.show', $pcnCase) }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>{{ $pcnCase->pcn_number }}</a>
                <span>/</span>
                <span>Edit</span>
            </div>
            <h1 class="truncate text-xl font-bold text-zinc-900 dark:text-white sm:text-2xl">Edit PCN {{ $pcnCase->pcn_number }}</h1>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('flux-admin.pcn.show', $pcnCase) }}" wire:navigate>
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save changes</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-4" novalidate>
        <section class="flux-admin-panel border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Case details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <x-flux-admin::field-group label="Customer" span="full" :error="$errors->first('form.customer_id')">
                    <div class="{{ count($customerSuggestions) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                        <flux:input wire:model.live.debounce.300ms="customerSearch" placeholder="Search by name or email…" autocomplete="off" />
                        @if(count($customerSuggestions))
                            <ul class="flux-admin-autocomplete-menu" role="listbox">
                                @foreach($customerSuggestions as $s)
                                    <li role="option" wire:mousedown.prevent="selectCustomer({{ $s['id'] }})">{{ $s['name'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="PCN number" required :error="$errors->first('form.pcn_number')">
                    <flux:input wire:model="form.pcn_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Date of contravention" required :error="$errors->first('form.date_of_contravention')">
                    <flux:input type="date" wire:model="form.date_of_contravention" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Time" required :error="$errors->first('form.time_of_contravention')">
                    <flux:input type="time" wire:model="form.time_of_contravention" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Date of letter issued" :error="$errors->first('form.date_of_letter_issued')">
                    <flux:input type="date" wire:model="form.date_of_letter_issued" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Full amount (£)" required :error="$errors->first('form.full_amount')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.full_amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Reduced amount (£)" :error="$errors->first('form.reduced_amount')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.reduced_amount" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Motorbike (reg)" required :error="$errors->first('form.motorbike_id')">
                    <div class="{{ count($motorbikeSuggestions) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                        <flux:input wire:model.live.debounce.300ms="motorbikeSearch" placeholder="Search by reg…" autocomplete="off"
                            x-on:keydown.enter.prevent="$wire.commitMotorbikeSearch()" />
                        @if(count($motorbikeSuggestions))
                            <ul class="flux-admin-autocomplete-menu" role="listbox">
                                @foreach($motorbikeSuggestions as $ms)
                                    <li role="option" wire:mousedown.prevent="selectMotorbike({{ $ms['id'] }})">{{ $ms['reg'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Payment link (council)" span="full" :error="$errors->first('form.council_link')">
                    <flux:input wire:model="form.council_link" placeholder="https://… or payment note" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Note" span="full" :error="$errors->first('form.note')">
                    <flux:textarea wire:model="form.note" rows="2" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 border-t border-zinc-200 pt-3 dark:border-zinc-700">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_police" class="accent-zinc-900 dark:accent-zinc-200"> Police notice
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.isClosed" class="accent-zinc-900 dark:accent-zinc-200"> Mark as closed
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="sendEmail" class="accent-zinc-900 dark:accent-zinc-200"> Send email on save
                </label>
            </div>
        </section>

        <section
            class="flux-admin-panel border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900"
            x-data="{ copied: false, copyLetter() { navigator.clipboard.writeText($refs.letter.value).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) }) } }"
        >
            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Copy liability letter</h2>
                <button
                    type="button"
                    x-on:click="copyLetter()"
                    class="shrink-0 border-0 bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700"
                    x-text="copied ? 'Copied' : 'Copy letter'"
                ></button>
            </div>
            <textarea x-ref="letter" readonly rows="8" class="max-h-48 w-full resize-y border border-zinc-200 bg-zinc-50 p-2.5 text-sm leading-snug text-zinc-800 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200">{{ $this->liabilityLetter }}</textarea>
        </section>

        <section class="flux-admin-panel overflow-hidden border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 bg-zinc-50 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Case updates</h2>
                <flux:button type="button" size="xs" variant="ghost" wire:click="addCaseUpdate" icon="plus" class="!rounded-none">Add update</flux:button>
            </div>

            @if(count($caseUpdates))
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($caseUpdates as $idx => $upd)
                        <div class="space-y-2 px-4 py-3" wire:key="upd-{{ $idx }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                    Update #{{ $idx + 1 }}
                                    @if(!empty($upd['id']))<span class="font-normal normal-case text-zinc-400">· ID {{ $upd['id'] }}</span>@endif
                                </span>
                                <button type="button" wire:click="removeCaseUpdate({{ $idx }})"
                                    class="text-xs text-red-500 transition hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">Remove</button>
                            </div>

                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                <x-flux-admin::field-group label="Date" :error="$errors->first('caseUpdates.'.$idx.'.update_date')">
                                    <flux:input type="date" wire:model="caseUpdates.{{ $idx }}.update_date" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Additional fee (£)" :error="$errors->first('caseUpdates.'.$idx.'.additional_fee')">
                                    <flux:input type="number" step="0.01" wire:model="caseUpdates.{{ $idx }}.additional_fee" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Notes" :error="$errors->first('caseUpdates.'.$idx.'.note')">
                                    <flux:input wire:model="caseUpdates.{{ $idx }}.note" />
                                </x-flux-admin::field-group>
                            </div>

                            <div class="grid grid-cols-2 gap-x-3 gap-y-1.5 sm:grid-cols-3 lg:grid-cols-5">
                                <label class="flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_appealed" class="accent-zinc-900"> Appealed
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_paid_by_owner" class="accent-zinc-900"> Paid by NGN
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_paid_by_keeper" class="accent-zinc-900"> Paid by hirer
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_transferred" class="accent-zinc-900"> Transferred
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <input type="checkbox" wire:model="caseUpdates.{{ $idx }}.is_cancled" class="accent-zinc-900"> Cancelled
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">No case updates yet. Click &ldquo;Add update&rdquo; to add one.</p>
            @endif
        </section>

        <div class="flex justify-end gap-2">
            <a href="{{ route('flux-admin.pcn.show', $pcnCase) }}" wire:navigate>
                <flux:button type="button" variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" size="sm" class="!rounded-none">Save changes</flux:button>
        </div>
    </form>
</div>
