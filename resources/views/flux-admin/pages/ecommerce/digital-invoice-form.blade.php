<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.digital-invoices.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Digital Invoices</a>
                <span>/</span>
                <span>{{ $digitalInvoice ? 'Edit' : 'New invoice' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $digitalInvoice ? 'Edit invoice: '.($digitalInvoice->invoice_number ?: '#'.$digitalInvoice->id) : 'New digital invoice' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.digital-invoices.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save invoice</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Invoice details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Invoice number" :error="$errors->first('form.invoice_number')">
                    <flux:input wire:model="form.invoice_number" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Invoice type" required :error="$errors->first('form.invoice_type')">
                    <flux:select wire:model="form.invoice_type">
                        <flux:select.option value="sale">Sale</flux:select.option>
                        <flux:select.option value="repair">Repair</flux:select.option>
                        <flux:select.option value="rental">Rental</flux:select.option>
                        <flux:select.option value="service">Service</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Category" :error="$errors->first('form.invoice_category')">
                    <flux:select wire:model="form.invoice_category" placeholder="— Select —">
                        <flux:select.option value="">None</flux:select.option>
                        <flux:select.option value="new">New Bike</flux:select.option>
                        <flux:select.option value="used">Used Bike</flux:select.option>
                        <flux:select.option value="parts">Parts</flux:select.option>
                        <flux:select.option value="service">Service</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Status" required :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="approved">Approved</flux:select.option>
                        <flux:select.option value="sent">Sent</flux:select.option>
                        <flux:select.option value="paid">Paid</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Booking invoice" :error="$errors->first('form.booking_invoice_id')">
                    <flux:select wire:model="form.booking_invoice_id" placeholder="— Select —">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($bookingInvoices as $bookingInvoice)
                            <flux:select.option value="{{ $bookingInvoice->id }}">
                                #{{ $bookingInvoice->id }} · Booking {{ $bookingInvoice->booking_id }} · £{{ number_format((float) $bookingInvoice->amount, 2) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Issue date" required :error="$errors->first('form.issue_date')">
                    <flux:input type="date" wire:model="form.issue_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Due date" :error="$errors->first('form.due_date')">
                    <flux:input type="date" wire:model="form.due_date" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Amount (£)" :error="$errors->first('form.amount')">
                    <flux:input type="number" step="0.01" wire:model="form.amount" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Total paid (£)" :error="$errors->first('form.total_paid')">
                    <flux:input type="number" step="0.01" wire:model="form.total_paid" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Customer & vehicle</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Customer" :error="$errors->first('form.customer_id')">
                    <flux:select wire:model.live="form.customer_id" placeholder="— Select —">
                        <flux:select.option value="">Manual customer</flux:select.option>
                        @foreach($customers as $customer)
                            <flux:select.option value="{{ $customer->id }}">
                                {{ trim($customer->first_name.' '.$customer->last_name) }} · {{ $customer->phone }} · {{ $customer->email }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer name" :error="$errors->first('form.customer_name')">
                    <flux:input wire:model="form.customer_name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer email" :error="$errors->first('form.customer_email')">
                    <flux:input type="email" wire:model="form.customer_email" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Customer phone" :error="$errors->first('form.customer_phone')">
                    <flux:input wire:model="form.customer_phone" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="WhatsApp" :error="$errors->first('form.whatsapp')">
                    <flux:input wire:model="form.whatsapp" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Motorbike" :error="$errors->first('form.motorbike_id')">
                    <flux:select wire:model.live="form.motorbike_id" placeholder="— Select —">
                        <flux:select.option value="">Manual vehicle</flux:select.option>
                        @foreach($motorbikes as $motorbike)
                            <flux:select.option value="{{ $motorbike->id }}">
                                {{ $motorbike->reg_no }} · {{ trim($motorbike->make.' '.$motorbike->model) }} · {{ $motorbike->year }} · {{ $motorbike->vin_number }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Registration number" :error="$errors->first('form.registration_number')">
                    <flux:input wire:model="form.registration_number" class="uppercase" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Make" :error="$errors->first('form.make')">
                    <flux:input wire:model="form.make" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('form.model')">
                    <flux:input wire:model="form.model" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Year" :error="$errors->first('form.year')">
                    <flux:input type="number" wire:model="form.year" min="1900" max="2100" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="VIN" :error="$errors->first('form.vin')">
                    <flux:input wire:model="form.vin" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Invoice items</h2>
                <flux:button type="button" size="sm" variant="subtle" icon="plus" wire:click="addItem" class="!rounded-none">Add item</flux:button>
            </div>
            <div class="space-y-4">
                @forelse($items as $index => $item)
                    <div class="grid grid-cols-1 gap-3 border border-zinc-200 p-3 dark:border-zinc-800 sm:grid-cols-2 lg:grid-cols-12" wire:key="invoice-item-{{ $index }}">
                        <div class="lg:col-span-3">
                            <x-flux-admin::field-group label="Item name" :error="$errors->first('items.'.$index.'.item_name')">
                                <flux:input wire:model="items.{{ $index }}.item_name" />
                            </x-flux-admin::field-group>
                        </div>
                        <div class="lg:col-span-2">
                            <x-flux-admin::field-group label="SKU" :error="$errors->first('items.'.$index.'.sku')">
                                <flux:input wire:model="items.{{ $index }}.sku" />
                            </x-flux-admin::field-group>
                        </div>
                        <div class="lg:col-span-1">
                            <x-flux-admin::field-group label="Qty" :error="$errors->first('items.'.$index.'.quantity')">
                                <flux:input type="number" min="1" wire:model.live.debounce.300ms="items.{{ $index }}.quantity" />
                            </x-flux-admin::field-group>
                        </div>
                        <div class="lg:col-span-2">
                            <x-flux-admin::field-group label="Price" :error="$errors->first('items.'.$index.'.price')">
                                <flux:input type="number" step="0.01" min="0" wire:model.live.debounce.300ms="items.{{ $index }}.price" />
                            </x-flux-admin::field-group>
                        </div>
                        <div class="lg:col-span-1">
                            <x-flux-admin::field-group label="Discount" :error="$errors->first('items.'.$index.'.discount')">
                                <flux:input type="number" step="0.01" min="0" wire:model.live.debounce.300ms="items.{{ $index }}.discount" />
                            </x-flux-admin::field-group>
                        </div>
                        <div class="lg:col-span-1">
                            <x-flux-admin::field-group label="Tax" :error="$errors->first('items.'.$index.'.tax')">
                                <flux:input type="number" step="0.01" min="0" wire:model.live.debounce.300ms="items.{{ $index }}.tax" />
                            </x-flux-admin::field-group>
                        </div>
                        <div class="lg:col-span-1">
                            <x-flux-admin::field-group label="Total">
                                <flux:input type="number" step="0.01" wire:model="items.{{ $index }}.total" readonly />
                            </x-flux-admin::field-group>
                        </div>
                        <div class="flex items-end lg:col-span-1">
                            <flux:button type="button" size="sm" variant="ghost" icon="trash" wire:click="removeItem({{ $index }})" class="!rounded-none text-red-600 dark:text-red-400">Remove</flux:button>
                        </div>
                        <div class="sm:col-span-2 lg:col-span-12">
                            <x-flux-admin::field-group label="Notes" :error="$errors->first('items.'.$index.'.notes')">
                                <flux:textarea rows="2" wire:model="items.{{ $index }}.notes" />
                            </x-flux-admin::field-group>
                        </div>
                    </div>
                @empty
                    <div class="border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        No invoice items.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Notes</h2>
            <div class="space-y-4">
                <x-flux-admin::field-group label="Customer notes" :error="$errors->first('form.notes')">
                    <flux:textarea wire:model="form.notes" rows="3" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Internal notes" :error="$errors->first('form.internal_notes')">
                    <flux:textarea wire:model="form.internal_notes" rows="3" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.digital-invoices.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save invoice</flux:button>
        </div>
    </form>
</div>
