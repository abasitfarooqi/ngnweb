<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-2">Book Your MOT Test</h1>
        <p class="text-gray-300">Quick, reliable MOT testing at all our London branches</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:card class="p-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">MOT Booking Form</h2>

        <form wire:submit="submitBooking" class="space-y-5">

            <flux:field>
                <flux:label>Select Branch *</flux:label>
                <flux:select wire:model="selectedBranch" variant="listbox" searchable placeholder="Choose a branch…">
                    @foreach($branches as $branch)
                        <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="selectedBranch" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Registration Number *</flux:label>
                    <flux:input wire:model="regNo" placeholder="AB12 CDE" class="uppercase tracking-wider font-bold" />
                    <flux:error name="regNo" />
                </flux:field>
                <flux:field>
                    <flux:label>Make</flux:label>
                    <flux:input wire:model="make" placeholder="e.g. Honda" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Model</flux:label>
                <flux:input wire:model="model" placeholder="e.g. CBR500R" />
            </flux:field>

            <flux:separator class="my-2" />
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">Your Details</h3>

            <flux:field>
                <flux:label>Full Name *</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Email *</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>
                <flux:field>
                    <flux:label>Phone *</flux:label>
                    <flux:input wire:model="phone" type="tel" />
                    <flux:error name="phone" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Preferred Date *</flux:label>
                    <flux:date-picker wire:model="preferredDate" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                    <flux:error name="preferredDate" />
                </flux:field>
                <flux:field>
                    <flux:label>Preferred Time *</flux:label>
                    <flux:select wire:model="preferredTime" variant="listbox" placeholder="Select time…">
                        @foreach(['09:00'=>'09:00 AM','10:00'=>'10:00 AM','11:00'=>'11:00 AM','12:00'=>'12:00 PM','14:00'=>'02:00 PM','15:00'=>'03:00 PM','16:00'=>'04:00 PM','17:00'=>'05:00 PM'] as $val => $label)
                            <flux:select.option value="{{ $val }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="preferredTime" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Additional Notes</flux:label>
                <flux:textarea wire:model="notes" rows="3" />
            </flux:field>

            <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                Submit MOT Booking
            </flux:button>
        </form>
    </flux:card>
</div>
</div>
