<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Add vehicle from DVLA</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Enter a UK VRM to pull vehicle details from the DVLA Vehicle Enquiry API and save the record.</p>
        </div>
        <flux:button href="{{ route('flux-admin.motorbikes.index') }}" wire:navigate variant="ghost" icon="arrow-left">Back to motorbikes</flux:button>
    </div>

    @if (session('status'))
        <div class="mb-4 border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <form wire:submit.prevent="lookupReg" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <flux:input
                        label="Registration"
                        wire:model="regInput"
                        placeholder="e.g. AB12 CDE"
                        description="Leave a VIN below if you have one. DVLA can only verify VRM."
                        class="uppercase"
                        autofocus
                    />
                </div>
                <flux:button type="submit" variant="primary" icon="magnifying-glass" :disabled="$isLooking">
                    {{ $isLooking ? 'Looking up…' : 'Look up DVLA' }}
                </flux:button>
            </form>

            @if ($lookupError)
                <div class="mt-4 border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                    {{ $lookupError }}
                </div>
            @endif

            @if ($alreadyExists && $savedMotorbikeId)
                <div class="mt-4 border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                    This VRM is already on file. <a class="underline" href="{{ route('flux-admin.motorbikes.show', $savedMotorbikeId) }}" wire:navigate>Open existing vehicle #{{ $savedMotorbikeId }}</a>.
                </div>
            @endif

            @if ($dvla)
                <div class="mt-6 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                    <h2 class="mb-3 text-lg font-semibold text-zinc-900 dark:text-white">DVLA response</h2>
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                        @foreach ([
                            'make' => 'Make',
                            'yearOfManufacture' => 'Year',
                            'colour' => 'Colour',
                            'engineCapacity' => 'Engine (cc)',
                            'fuelType' => 'Fuel type',
                            'wheelplan' => 'Wheelplan',
                            'typeApproval' => 'Type approval',
                            'co2Emissions' => 'CO₂ emissions',
                            'motStatus' => 'MOT status',
                            'motExpiryDate' => 'MOT expiry',
                            'taxStatus' => 'Tax status',
                            'taxDueDate' => 'Tax due',
                            'monthOfFirstRegistration' => 'First registered',
                            'markedForExport' => 'Marked for export',
                            'dateOfLastV5CIssued' => 'Last V5C',
                        ] as $key => $label)
                            <div class="flex justify-between border-b border-zinc-100 py-1 dark:border-zinc-800">
                                <dt class="text-zinc-500">{{ $label }}</dt>
                                <dd class="font-medium">{{ is_bool($dvla[$key] ?? null) ? ($dvla[$key] ? 'Yes' : 'No') : ($dvla[$key] ?? '—') }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <form wire:submit.prevent="saveVehicle" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <flux:input label="Model (required)" wire:model="model" description="DVLA does not return the model for every VRM." />
                        <flux:input label="VIN (optional)" wire:model="vinNumber" />
                        <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 sm:col-span-2">
                            <input type="checkbox" wire:model="internal" class="h-4 w-4" />
                            Internal vehicle (profile 1)
                        </label>
                        <div class="sm:col-span-2 flex justify-end">
                            <flux:button type="submit" variant="primary" icon="check">Save vehicle</flux:button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <aside class="border border-zinc-200 bg-zinc-50 p-5 text-sm dark:border-zinc-800 dark:bg-zinc-950">
            <h3 class="mb-3 font-semibold text-zinc-900 dark:text-white">How it works</h3>
            <ol class="list-decimal space-y-2 pl-5 text-zinc-600 dark:text-zinc-400">
                <li>Type a UK VRM and click <em>Look up DVLA</em>.</li>
                <li>We check if the reg is already in the fleet; if so, you'll be sent to the existing record.</li>
                <li>Otherwise we fetch make, year, colour, engine, MOT and tax status.</li>
                <li>Confirm the <em>model</em> (DVLA does not always return it) and save.</li>
                <li>We create the motorbike, its active registration, and an annual compliance row in one go.</li>
            </ol>
            <p class="mt-4 text-xs text-zinc-500">Requires <code>services.dvla.api_key</code> to be configured.</p>
        </aside>
    </div>
</div>
