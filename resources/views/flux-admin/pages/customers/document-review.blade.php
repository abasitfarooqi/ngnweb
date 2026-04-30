<div class="flex flex-col gap-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Review document</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Approve, reject, or set an expiry. Approval auto-generates a document number.
            </p>
        </div>
        <flux:button size="sm" variant="ghost" :href="route('flux-admin.customer-documents.index')" class="!rounded-none">Back to queue</flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-2 border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Details</h2>
            <dl class="text-sm divide-y divide-zinc-100 dark:divide-zinc-800">
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Customer</dt><dd class="text-zinc-900 dark:text-white text-right">
                    @if($document->customer)
                        {{ trim(($document->customer->first_name ?? '').' '.($document->customer->last_name ?? '')) ?: $document->customer->email }}
                    @else — @endif
                </dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Document type</dt><dd class="text-zinc-900 dark:text-white">{{ $document->documentType?->name ?? '—' }}</dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">File</dt><dd class="text-zinc-900 dark:text-white text-right max-w-[14rem] truncate">{{ $document->file_name ?? '—' }}</dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Uploaded</dt><dd class="text-zinc-900 dark:text-white">{{ $document->created_at?->format('d M Y H:i') }}</dd></div>
                <div class="flex justify-between py-2"><dt class="text-zinc-500">Current status</dt><dd><x-flux-admin::status-badge :status="$document->status" /></dd></div>
                @if($document->document_number)
                    <div class="flex justify-between py-2"><dt class="text-zinc-500">Document number</dt><dd class="font-mono text-xs text-zinc-900 dark:text-white">{{ $document->document_number }}</dd></div>
                @endif
                @if($document->reviewer_id)
                    <div class="flex justify-between py-2"><dt class="text-zinc-500">Last reviewer</dt><dd class="text-zinc-900 dark:text-white">#{{ $document->reviewer_id }} at {{ optional($document->reviewed_at)?->format('d M Y H:i') }}</dd></div>
                @endif
            </dl>

            @if($document->file_url)
                <div class="mt-4 flex items-center gap-2">
                    <flux:button size="sm" variant="primary" :href="$document->file_url" target="_blank" icon="eye" class="!rounded-none">Open file</flux:button>
                </div>
            @endif
        </div>

        <div class="lg:col-span-3">
            <x-flux-admin::form-panel>
                @if(session('flux-admin.flash'))
                    <div class="mb-4 border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                        {{ session('flux-admin.flash') }}
                    </div>
                @endif

                <form wire:submit.prevent="save" class="flex flex-col gap-4">
                    <x-flux-admin::field-group label="Status" required :error="$errors->first('status')">
                        <flux:select wire:model.live="status">
                            <flux:select.option value="pending_review">Pending review</flux:select.option>
                            <flux:select.option value="uploaded">Uploaded</flux:select.option>
                            <flux:select.option value="approved">Approved</flux:select.option>
                            <flux:select.option value="rejected">Rejected (require reupload)</flux:select.option>
                            <flux:select.option value="archived">Archived</flux:select.option>
                        </flux:select>
                    </x-flux-admin::field-group>

                    <x-flux-admin::field-group label="Valid until" :error="$errors->first('valid_until')" hint="Optional expiry. Leave blank if the document does not expire.">
                        <flux:input type="date" wire:model="valid_until" />
                    </x-flux-admin::field-group>

                    @if($status === 'rejected')
                        <x-flux-admin::field-group label="Rejection reason" required :error="$errors->first('rejection_reason')" hint="Shown to the customer so they can correct and reupload.">
                            <flux:textarea wire:model="rejection_reason" rows="3" />
                        </x-flux-admin::field-group>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <flux:button size="sm" variant="ghost" :href="route('flux-admin.customer-documents.index')" class="!rounded-none">Cancel</flux:button>
                        <flux:button size="sm" variant="primary" type="submit" class="!rounded-none">Save decision</flux:button>
                    </div>
                </form>
            </x-flux-admin::form-panel>
        </div>
    </div>
</div>
