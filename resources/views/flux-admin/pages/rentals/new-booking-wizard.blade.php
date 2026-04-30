<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">New rental booking</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Six steps: motorbike, customer, terms, payment, documents, done.</p>
        </div>
        <flux:button href="{{ route('flux-admin.rentals.index') }}" wire:navigate variant="ghost" icon="arrow-left">Back to rentals</flux:button>
    </div>

    @if (session('status'))
        <div class="mb-4 border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    {{-- Stepper --}}
    <nav class="mb-6 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ([1 => 'Motorbike', 2 => 'Customer', 3 => 'Terms', 4 => 'Payment', 5 => 'Documents', 6 => 'Done'] as $n => $label)
            @php $isDone = $step > $n; $isActive = $step === $n; @endphp
            <button type="button"
                wire:click="goToStep({{ $n }})"
                class="border px-3 py-2 text-left text-xs sm:text-sm transition
                    {{ $isActive ? 'border-zinc-900 bg-zinc-900 text-white dark:border-white dark:bg-white dark:text-zinc-900' : '' }}
                    {{ $isDone ? 'border-emerald-500 bg-emerald-50 text-emerald-900 dark:border-emerald-600 dark:bg-emerald-950 dark:text-emerald-100' : '' }}
                    {{ !$isActive && !$isDone ? 'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-400 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300' : '' }}">
                <span class="font-mono text-[10px] opacity-70">STEP {{ $n }} @if($isDone) ✓ @endif</span>
                <span class="block font-semibold">{{ $label }}</span>
            </button>
        @endforeach
    </nav>

    {{-- STEP 1 — Motorbike --}}
    @if ($step === 1)
        <div class="border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center gap-3">
                <flux:input class="flex-1" wire:model.live.debounce.300ms="bikeSearch" placeholder="Search by registration, make or model…" variant="filled" icon="magnifying-glass" />
                <span class="text-xs text-zinc-500">{{ $motorbikes->count() }} available</span>
            </div>

            @error('motorbikeId') <p class="mb-2 text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Reg</flux:table.column>
                        <flux:table.column>Make / model</flux:table.column>
                        <flux:table.column>Year</flux:table.column>
                        <flux:table.column>Weekly rent</flux:table.column>
                        <flux:table.column>Type</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($motorbikes as $m)
                            <flux:table.row wire:key="bike-{{ $m->id }}">
                                <flux:table.cell class="font-mono">{{ $m->reg_no ?: '—' }}</flux:table.cell>
                                <flux:table.cell>{{ $m->make }} {{ $m->model }}</flux:table.cell>
                                <flux:table.cell>{{ $m->year }}</flux:table.cell>
                                <flux:table.cell>
                                    @if($m->weekly_rent !== null && (float) $m->weekly_rent > 0)
                                        £{{ number_format((float) $m->weekly_rent, 2) }}
                                    @else
                                        <span class="text-xs text-amber-600 dark:text-amber-400">set on step 3</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($m->is_ebike)
                                        <flux:badge color="emerald" size="sm">E-bike</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Petrol</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="sm" wire:click="selectMotorbike({{ $m->id }}, {{ (float) ($m->weekly_rent ?? 0) }})">Select</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="py-8 text-center text-sm text-zinc-500">No available motorbikes match this search.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    @endif

    {{-- STEP 2 — Customer --}}
    @if ($step === 2)
        <div class="border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex flex-col gap-2 text-sm text-zinc-600 dark:text-zinc-300 sm:flex-row sm:items-center sm:justify-between">
                <span>
                    Vehicle:
                    <strong>{{ $selectedMotorbike?->reg_no ?: '—' }}</strong>
                    — {{ $selectedMotorbike?->make }} {{ $selectedMotorbike?->model }}
                </span>
                <flux:button variant="ghost" size="sm" wire:click="goToStep(1)" icon="arrow-left">Change bike</flux:button>
            </div>

            <div class="mb-4 flex items-center gap-3">
                <flux:input class="flex-1" wire:model.live.debounce.300ms="customerSearch" placeholder="Search by name, email or phone…" variant="filled" icon="magnifying-glass" />
                <span class="text-xs text-zinc-500">{{ $customers->count() }} match</span>
            </div>

            @error('customerId') <p class="mb-2 text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Name</flux:table.column>
                        <flux:table.column>Email</flux:table.column>
                        <flux:table.column>Phone</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($customers as $c)
                            <flux:table.row wire:key="customer-{{ $c->id }}">
                                <flux:table.cell>{{ $c->first_name }} {{ $c->last_name }}</flux:table.cell>
                                <flux:table.cell>{{ $c->email }}</flux:table.cell>
                                <flux:table.cell>{{ $c->phone }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="sm" wire:click="selectCustomer({{ $c->id }})">Select</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="py-8 text-center text-sm text-zinc-500">No customers match this search.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    @endif

    {{-- STEP 3 — Terms --}}
    @if ($step === 3)
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-white">Rental terms</h2>

                <form wire:submit.prevent="confirmTerms" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input type="date" label="Start date" wire:model="startDate" />
                    <flux:input type="number" step="0.01" min="0" label="Weekly rent (£)" wire:model="weeklyRent" description="{{ ($weeklyRent ?? 0) > 0 ? 'Pricing loaded from the bike.' : 'No pricing set — enter the agreed rate.' }}" />
                    <div class="sm:col-span-2">
                        <flux:textarea label="Notes (optional)" wire:model="notes" rows="3" placeholder="Collection site, accessories, agreed extras…" />
                    </div>
                    <div class="sm:col-span-2 border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950">
                        <h3 class="mb-2 text-sm font-semibold text-zinc-900 dark:text-white">Terms &amp; conditions</h3>
                        <ul class="mb-3 list-disc space-y-1 pl-5 text-xs text-zinc-600 dark:text-zinc-400">
                            <li>Weekly rent is billed each week from the start date.</li>
                            <li>Motorbike must be returned in the condition it was issued, with all accessories.</li>
                            <li>Damage, PCNs and additional charges are the customer's responsibility.</li>
                            <li>Booking is created in <strong>DRAFT</strong> and becomes active once documents and payment are confirmed.</li>
                        </ul>
                        <label class="flex items-start gap-2 text-sm">
                            <input type="checkbox" wire:model.live="termsAccepted" class="mt-1">
                            <span>I confirm the customer has read and agreed to the rental terms.</span>
                        </label>
                        @error('termsAccepted') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2 flex items-center justify-between pt-2">
                        <flux:button type="button" variant="ghost" wire:click="goToStep(2)" icon="arrow-left">Change customer</flux:button>
                        <flux:button type="submit" variant="primary">Continue to payment</flux:button>
                    </div>
                </form>
            </div>

            @include('flux-admin.pages.rentals.partials.new-booking-summary', ['selectedMotorbike' => $selectedMotorbike, 'selectedCustomer' => $selectedCustomer, 'weeklyRent' => $weeklyRent, 'deposit' => $deposit, 'initialPayment' => $initialPayment, 'startDate' => $startDate])
        </div>
    @endif

    {{-- STEP 4 — Payment --}}
    @if ($step === 4)
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-white">Initial payment</h2>

                <form wire:submit.prevent="confirmPayment" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input type="number" step="0.01" min="0" label="Deposit (£)" wire:model="deposit" />
                    <flux:input type="number" step="0.01" min="0" label="Initial payment received (£)" wire:model="initialPayment" description="Marks deposit as paid when ≥ deposit amount." />
                    <flux:select label="Payment method" wire:model="paymentMethod">
                        <flux:select.option value="cash">Cash</flux:select.option>
                        <flux:select.option value="card">Card</flux:select.option>
                        <flux:select.option value="bank">Bank transfer</flux:select.option>
                        <flux:select.option value="none">Not collected yet</flux:select.option>
                    </flux:select>
                    <div class="sm:col-span-2 border border-zinc-200 bg-zinc-50 p-4 text-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <div class="flex justify-between"><span class="text-zinc-500">Weekly rent</span><span>£{{ number_format((float) ($weeklyRent ?? 0), 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-zinc-500">Deposit</span><span>£{{ number_format((float) $deposit, 2) }}</span></div>
                        <div class="mt-1 flex justify-between border-t border-zinc-200 pt-1 font-semibold dark:border-zinc-800">
                            <span>Total due today</span>
                            <span>£{{ number_format((float) ($weeklyRent + $deposit), 2) }}</span>
                        </div>
                        <div class="mt-1 flex justify-between text-emerald-700 dark:text-emerald-400">
                            <span>Received</span>
                            <span>£{{ number_format((float) $initialPayment, 2) }}</span>
                        </div>
                        <div class="mt-1 flex justify-between">
                            <span class="text-zinc-500">Balance</span>
                            <span>£{{ number_format(max(0, (float) ($weeklyRent + $deposit - $initialPayment)), 2) }}</span>
                        </div>
                    </div>
                    <div class="sm:col-span-2 flex items-center justify-between pt-2">
                        <flux:button type="button" variant="ghost" wire:click="goToStep(3)" icon="arrow-left">Back to terms</flux:button>
                        <flux:button type="submit" variant="primary">Continue to documents</flux:button>
                    </div>
                </form>
            </div>

            @include('flux-admin.pages.rentals.partials.new-booking-summary', ['selectedMotorbike' => $selectedMotorbike, 'selectedCustomer' => $selectedCustomer, 'weeklyRent' => $weeklyRent, 'deposit' => $deposit, 'initialPayment' => $initialPayment, 'startDate' => $startDate])
        </div>
    @endif

    {{-- STEP 5 — Documents / confirm --}}
    @if ($step === 5)
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="mb-2 text-lg font-semibold text-zinc-900 dark:text-white">Required documents</h2>
                <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">The booking will be created in DRAFT. Verify or collect these after creation on the booking detail page.</p>

                <ul class="mb-4 space-y-2 text-sm">
                    @foreach (['Driving licence (DVLA)', 'Proof of address', 'Passport / ID', 'Insurance certificate', 'Signed rental agreement'] as $doc)
                        <li class="flex items-center gap-2 border border-zinc-200 bg-zinc-50 px-3 py-2 dark:border-zinc-800 dark:bg-zinc-950">
                            <flux:icon name="document-text" class="h-4 w-4 text-zinc-500" />
                            <span>{{ $doc }}</span>
                        </li>
                    @endforeach
                </ul>

                <label class="flex items-start gap-2 text-sm">
                    <input type="checkbox" wire:model.live="sendDocUploadLink" class="mt-1">
                    <span>Generate a secure upload link for the customer after creating the booking.</span>
                </label>

                <div class="mt-5 flex items-center justify-between">
                    <flux:button type="button" variant="ghost" wire:click="goToStep(4)" icon="arrow-left">Back to payment</flux:button>
                    <flux:button variant="primary" wire:click="createBooking" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createBooking">Create booking</span>
                        <span wire:loading wire:target="createBooking">Creating…</span>
                    </flux:button>
                </div>
            </div>

            @include('flux-admin.pages.rentals.partials.new-booking-summary', ['selectedMotorbike' => $selectedMotorbike, 'selectedCustomer' => $selectedCustomer, 'weeklyRent' => $weeklyRent, 'deposit' => $deposit, 'initialPayment' => $initialPayment, 'startDate' => $startDate])
        </div>
    @endif

    {{-- STEP 6 — Done --}}
    @if ($step === 6 && $createdBookingId)
        <div class="border border-emerald-200 bg-emerald-50 p-8 text-center dark:border-emerald-900 dark:bg-emerald-950">
            <flux:icon name="check-circle" class="mx-auto h-12 w-12 text-emerald-600 dark:text-emerald-400" />
            <h2 class="mt-3 text-xl font-semibold text-emerald-900 dark:text-emerald-100">Booking #{{ $createdBookingId }} created</h2>
            <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-200">Open the booking to verify documents, collect signature and post invoices.</p>

            @if ($sendDocUploadLink && $docUploadLink)
                <div class="mx-auto mt-4 max-w-xl border border-emerald-300 bg-white p-3 text-left text-xs dark:border-emerald-800 dark:bg-zinc-900">
                    <div class="font-semibold text-emerald-900 dark:text-emerald-200">Document upload link (share with customer):</div>
                    <a href="{{ $docUploadLink }}" target="_blank" class="break-all text-emerald-700 underline dark:text-emerald-300">{{ $docUploadLink }}</a>
                </div>
            @endif

            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                <flux:button variant="primary" href="{{ route('flux-admin.rentals.show', $createdBookingId) }}" wire:navigate>Open booking</flux:button>
                <flux:button variant="ghost" href="{{ route('flux-admin.new-booking.index') }}" wire:navigate>Create another</flux:button>
            </div>
        </div>
    @endif
</div>
