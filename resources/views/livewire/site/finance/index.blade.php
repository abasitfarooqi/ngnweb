<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Motorcycle Finance</h1>
        <p class="text-gray-300 text-lg mb-6">Flexible finance options to help you get on the road sooner</p>
        <flux:button href="/bikes" variant="outline" class="border-white text-white hover:bg-white hover:text-gray-900">
            Browse Bikes For Sale
        </flux:button>
    </div>
</div>

{{-- How it works --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">How It Works</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
        @foreach([
            ['num'=>'1', 'title'=>'Choose Your Bike', 'text'=>'Browse our selection of new and used motorcycles and find the one that suits you.'],
            ['num'=>'2', 'title'=>'Apply for Finance', 'text'=>'Fill in our quick finance application form. Decision usually within 24 hours.'],
            ['num'=>'3', 'title'=>'Ride Away', 'text'=>'Once approved, complete your paperwork and ride away on your new bike.'],
        ] as $step)
            <div>
                <div class="w-14 h-14 bg-brand-red text-white flex items-center justify-center text-2xl font-bold mx-auto mb-4">{{ $step['num'] }}</div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $step['text'] }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- Finance calculator --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Finance Calculator</h2>

        <flux:card class="p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <flux:field>
                    <flux:label>Bike Price (£)</flux:label>
                    <flux:input wire:model.live="loanAmount" type="number" min="500" max="30000" />
                </flux:field>
                <flux:field>
                    <flux:label>Deposit (£)</flux:label>
                    <flux:input wire:model.live="deposit" type="number" min="0" />
                </flux:field>
                <flux:field>
                    <flux:label>Term (months)</flux:label>
                    <flux:select wire:model.live="term" variant="listbox" placeholder="Select term">
                        @foreach([12, 24, 36, 48, 60] as $t)
                            <flux:select.option value="{{ $t }}">{{ $t }} months</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Interest Rate (% APR)</flux:label>
                    <flux:input wire:model.live="rate" type="number" step="0.1" />
                </flux:field>
            </div>

            @if($monthlyPayment)
                <div class="bg-gray-50 dark:bg-gray-800 p-6 text-center">
                    <p class="text-sm text-gray-500 mb-1">Estimated monthly payment</p>
                    <p class="text-4xl font-bold text-brand-red">£{{ number_format($monthlyPayment, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Representative figure. Actual rate subject to status.</p>
                </div>
            @endif
        </flux:card>
    </div>
</div>

{{-- Application form --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Apply for Finance</h2>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit="submitApplication" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>First Name *</flux:label>
                <flux:input wire:model="firstName" />
                <flux:error name="firstName" />
            </flux:field>
            <flux:field>
                <flux:label>Last Name *</flux:label>
                <flux:input wire:model="lastName" />
                <flux:error name="lastName" />
            </flux:field>
        </div>
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
        <flux:field>
            <flux:label>Employment Status</flux:label>
            <flux:select wire:model="employmentStatus" variant="listbox" placeholder="Select…">
                @foreach(['employed'=>'Employed', 'self_employed'=>'Self-Employed', 'retired'=>'Retired', 'student'=>'Student', 'other'=>'Other'] as $val => $label)
                    <flux:select.option value="{{ $val }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:label>Bike You're Interested In</flux:label>
            <flux:input wire:model="bikeInterest" placeholder="e.g. Honda CB500F 2023" />
        </flux:field>
        <flux:field>
            <flux:label>Additional Information</flux:label>
            <flux:textarea wire:model="notes" rows="3" />
        </flux:field>
        <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
            <input type="checkbox" wire:model="consent" class="mt-0.5 accent-brand-red" />
            <span>I consent to NGN Motors contacting me about this application *</span>
        </label>
        <flux:error name="consent" />
        <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
            Submit Finance Application
        </flux:button>
        <p class="text-xs text-gray-500 text-center">
            Finance subject to status. NGN Motors is a credit broker, not a lender.
        </p>
    </form>
</div>
</div>
