<div class="{{ $invoiceId ? 'mt-4' : '' }}">
    @if($flashMessage)
        <div class="mb-3 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    @if($invoiceId)
        <section class="overflow-hidden border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 bg-zinc-100 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">This invoice</h2>
            </div>

            <div class="space-y-3 p-4">
                <div class="weekly-update-form flex w-full min-w-0 flex-col gap-2 lg:flex-row lg:items-end">
                    <div class="weekly-update-date w-full shrink-0 lg:w-40">
                        <label class="mb-1 block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Date</label>
                        <input
                            type="date"
                            wire:model="newNotedDate"
                            class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-red dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                        />
                    </div>
                    <div class="weekly-update-time w-full shrink-0 lg:w-32">
                        <label class="mb-1 block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Time</label>
                        <input
                            type="time"
                            wire:model="newNotedTime"
                            class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-red dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                        />
                    </div>
                    <div class="weekly-update-note min-w-0 w-full flex-1">
                        <label class="mb-1 block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Note</label>
                        <input
                            type="text"
                            wire:model="newNote"
                            placeholder="e.g. Customer said they will pay on Friday"
                            class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-red dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                        />
                        @error('newNote') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button
                        type="button"
                        wire:click="addInvoiceUpdate"
                        wire:confirm="Email this note to the customer and copy customer service? Click Cancel if you are not sure."
                        class="weekly-update-save inline-flex w-full shrink-0 items-center justify-center px-3 py-2 text-xs font-semibold bg-emerald-600 text-white transition hover:bg-emerald-700 lg:h-10 lg:w-auto"
                    >
                        Add update
                    </button>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Leave date or time blank to use now. Add update asks you to confirm before the customer is emailed.</p>

                @forelse($updates as $update)
                    <div class="border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800/60" wire:key="inv-upd-{{ $update->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $update->created_at?->format('d M Y H:i') }}
                                    · {{ trim(($update->user?->first_name ?? '').' '.($update->user?->last_name ?? '')) ?: 'Staff' }}
                                </p>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $update->note }}</p>
                            </div>
                            <button
                                type="button"
                                wire:click="removeUpdate({{ $update->id }})"
                                wire:confirm="Remove this update?"
                                class="text-xs text-red-500 transition hover:text-red-700 dark:text-red-400"
                            >Remove</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No updates on this invoice yet.</p>
                @endforelse
            </div>
        </section>
    @else
        <section class="overflow-hidden border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 bg-zinc-100 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">Weekly rental updates</h2>
                <flux:button type="button" size="xs" variant="ghost" wire:click="addDraft" icon="plus" class="!rounded-none">Add update</flux:button>
            </div>

            @if(count($drafts))
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($drafts as $idx => $upd)
                        <div class="space-y-2 px-4 py-3" wire:key="wk-upd-{{ $upd['id'] ?? 'new-'.$idx }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                    Update #{{ $idx + 1 }}
                                    @if(!empty($upd['id']))<span class="font-normal normal-case text-zinc-400">· ID {{ $upd['id'] }}</span>@endif
                                    @if(!empty($upd['invoice_id']))<span class="font-normal normal-case text-zinc-400">· Invoice #{{ $upd['invoice_id'] }}</span>@endif
                                </span>
                                <button type="button" wire:click="removeDraft({{ $idx }})"
                                    class="text-xs text-red-500 transition hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">Remove</button>
                            </div>
                            <div class="weekly-update-form flex w-full min-w-0 flex-col gap-2 lg:flex-row lg:items-end">
                                <x-flux-admin::field-group label="Date" class="weekly-update-date w-full shrink-0 lg:w-40" :error="$errors->first('drafts.'.$idx.'.noted_date')">
                                    <flux:input type="date" wire:model="drafts.{{ $idx }}.noted_date" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Time" class="weekly-update-time w-full shrink-0 lg:w-32" :error="$errors->first('drafts.'.$idx.'.noted_time')">
                                    <flux:input type="time" wire:model="drafts.{{ $idx }}.noted_time" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Note" class="weekly-update-note min-w-0 w-full flex-1" :error="$errors->first('drafts.'.$idx.'.note')">
                                    <flux:input wire:model="drafts.{{ $idx }}.note" placeholder="e.g. Called customer, no answer" />
                                </x-flux-admin::field-group>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">No weekly rental updates yet. Click &ldquo;Add update&rdquo; to add a general booking note.</p>
            @endif

            <div class="flex justify-end gap-2 border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                <flux:button type="button" variant="ghost" size="sm" class="!rounded-none" wire:click="loadDrafts">Cancel</flux:button>
                <flux:button type="button" variant="primary" size="sm" class="!rounded-none" wire:click="saveDrafts">Save changes</flux:button>
            </div>

            @if($updates->isNotEmpty())
                <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-zinc-500">History</p>
                    <div class="space-y-3">
                        @foreach($updates as $update)
                            <div class="flex items-start justify-between gap-3 border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800/60" wire:key="wk-hist-{{ $update->id }}">
                                <div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $update->created_at?->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-zinc-900 dark:text-white">{{ $update->note }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        @if($update->invoice_id)
                                            Invoice #{{ $update->invoice_id }}
                                            ·
                                        @endif
                                        {{ trim(($update->user?->first_name ?? '').' '.($update->user?->last_name ?? '')) ?: 'Staff' }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeUpdate({{ $update->id }})"
                                    wire:confirm="Remove this update?"
                                    class="text-xs text-red-500 transition hover:text-red-700 dark:text-red-400"
                                >Remove</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($isSuperAdmin && $auditLogs->isNotEmpty())
                <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-zinc-500">Audit log · Super Admin only</p>
                    <div class="space-y-2 text-xs text-zinc-600 dark:text-zinc-400">
                        @foreach($auditLogs as $log)
                            <div class="border border-zinc-200 p-2 dark:border-zinc-700">
                                <p>
                                    {{ $log->action }}
                                    · {{ $log->created_at?->format('d M Y H:i') }}
                                    · {{ trim(($log->changer?->first_name ?? '').' '.($log->changer?->last_name ?? '')) ?: ('User #'.($log->changed_by ?: '—')) }}
                                    · update {{ $log->renting_weekly_update_id ?: '—' }}
                                </p>
                                @if($log->old_data)
                                    <p>Old: {{ json_encode($log->old_data) }}</p>
                                @endif
                                @if($log->new_data)
                                    <p>New: {{ json_encode($log->new_data) }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif
</div>
