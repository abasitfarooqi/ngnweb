<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.mot-bookings.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">MOT bookings</a>
                <span>/</span>
                <span>{{ $motBooking && $motBooking->exists ? 'Edit' : 'New' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $motBooking && $motBooking->exists ? 'Edit MOT booking' : 'New MOT booking' }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.mot-bookings.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Booking details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Customer name" required :error="$errors->first('form.customer_name')">
                    <flux:input wire:model="form.customer_name" placeholder="Full name" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Vehicle registration" required :error="$errors->first('form.vehicle_registration')">
                    <flux:input wire:model="form.vehicle_registration" placeholder="VRM" class="uppercase" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Contact number" required :error="$errors->first('form.customer_contact')">
                    <flux:input wire:model="form.customer_contact" placeholder="Phone number" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" required :error="$errors->first('form.customer_email')">
                    <flux:input type="email" wire:model="form.customer_email" placeholder="Email address" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Branch" required :error="$errors->first('form.branch_id')">
                    <input type="hidden" wire:model="form.branch_id">
                    <div class="border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                        {{ $branch?->name ?? 'Catford' }}
                    </div>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Status" required :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="booked">Booked</flux:select.option>
                        <flux:select.option value="available">Available</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Slot start" required :error="$errors->first('form.start')">
                    <flux:input type="datetime-local" wire:model.live="form.start" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Slot end (auto)" :error="$errors->first('form.end')">
                    <flux:input type="datetime-local" wire:model="form.end" readonly />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Payment method" required :error="$errors->first('form.payment_method_choice') ?: $errors->first('form.payment_method_custom')">
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <flux:select wire:model.live="form.payment_method_choice">
                            <flux:select.option value="">— Select —</flux:select.option>
                            <flux:select.option value="Cash">Cash</flux:select.option>
                            <flux:select.option value="Card">Card</flux:select.option>
                            <flux:select.option value="Other">Other</flux:select.option>
                        </flux:select>
                        @if(($form['payment_method_choice'] ?? '') === 'Other')
                            <flux:input wire:model="form.payment_method_custom" placeholder="Custom method" />
                        @endif
                    </div>
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Payment link" span="2" :error="$errors->first('form.payment_link')">
                    <flux:input wire:model="form.payment_link" placeholder="https://…" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Payment notes" required :error="$errors->first('form.payment_notes')">
                    <flux:textarea wire:model="form.payment_notes" rows="2" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Notes" required :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" placeholder="Internal notes…" rows="3" />
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <input type="checkbox" wire:model="form.is_paid" id="is_paid" class="accent-zinc-900 dark:accent-zinc-200">
                <label for="is_paid" class="text-sm text-zinc-700 dark:text-zinc-300 cursor-pointer">Payment received</label>
            </div>

            @if($motBooking && $motBooking->exists)
                <div class="mt-4 flex items-center gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    <input type="checkbox" wire:model="form.is_dealt" id="is_dealt" class="accent-zinc-900 dark:accent-zinc-200">
                    <label for="is_dealt" class="text-sm text-zinc-700 dark:text-zinc-300 cursor-pointer">Mark as dealt</label>
                </div>
            @endif
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.mot-bookings.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
        </div>
    </form>
</div>
