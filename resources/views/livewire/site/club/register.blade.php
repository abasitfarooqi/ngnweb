<div>

{{-- Hero --}}
<div class="bg-gray-900 text-white py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-amber-400 text-3xl mb-2">★</p>
        <h1 class="text-3xl md:text-4xl font-bold mb-3">Join NGN Club</h1>
        <p class="text-gray-300 text-sm">Free membership · Loyalty rewards · MOT reminders · Priority booking</p>
        <p class="mt-4 text-sm text-gray-400">
            Already a member?
            <a href="{{ route('ngnclub.login') }}" class="text-amber-400 hover:text-amber-300 font-medium underline">Login here</a>
        </p>
    </div>
</div>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-8">
            <flux:callout.heading>You're in!</flux:callout.heading>
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
        <div class="text-center">
            <a href="{{ route('ngnclub.login') }}"
               class="inline-flex items-center gap-2 px-8 py-3 bg-brand-red text-white font-semibold hover:bg-red-700 transition">
                Login to My Club
            </a>
        </div>
    @else
        <flux:card class="p-8">

            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Your Details</h2>

            <form wire:submit="joinClub" class="space-y-5">

                {{-- Personal details --}}
                <flux:field>
                    <flux:label>Full Name *</flux:label>
                    <flux:input wire:model="full_name" placeholder="John Smith" autocomplete="name" />
                    <flux:error name="full_name" />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Email Address *</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="john@example.com" autocomplete="email" />
                        <flux:error name="email" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Phone Number *</flux:label>
                        <flux:input wire:model="phone" type="tel" placeholder="07700 900 123" autocomplete="tel" />
                        <flux:error name="phone" />
                        <flux:description>We'll send your passkey here via SMS</flux:description>
                    </flux:field>
                </div>

                <flux:separator />

                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Your Motorbike <span class="font-normal text-gray-400">(optional)</span></p>

                    {{-- UK plate styled input --}}
                    <flux:field class="mb-4">
                        <flux:label>Registration Number</flux:label>
                        <flux:input wire:model="vrm"
                            placeholder="AB12 CDE"
                            class="uppercase tracking-widest font-black text-base bg-yellow-50 dark:bg-yellow-900/10 border-2 border-yellow-400 focus:border-yellow-500" />
                        <flux:error name="vrm" />
                    </flux:field>

                    <div class="grid grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Make</flux:label>
                            <flux:input wire:model="make" placeholder="Honda" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Model</flux:label>
                            <flux:input wire:model="model" placeholder="PCX 125" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Year</flux:label>
                            <flux:input wire:model="year" placeholder="2022" maxlength="4" />
                            <flux:error name="year" />
                        </flux:field>
                    </div>
                </div>

                <flux:separator />

                {{-- Benefits reminder --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach([
                        ['★', 'Loyalty Rewards'],
                        ['🔔', 'MOT & Tax Alerts'],
                        ['🎁', 'Birthday Treat'],
                        ['⚡', 'Priority Booking'],
                        ['👥', 'Referral Bonuses'],
                        ['🏷️', 'Member Discounts'],
                    ] as [$icon, $label])
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                            <span>{{ $icon }}</span>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>

                <flux:separator />

                {{-- T&Cs --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="tc_agreed" class="mt-0.5 w-4 h-4 accent-brand-red flex-shrink-0" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        I agree to the
                        <a href="{{ route('ngnclub.terms') }}" target="_blank" class="text-brand-red hover:underline font-medium">Terms &amp; Conditions</a>
                        and I consent to receiving NGN Club communications including SMS *
                    </span>
                </label>
                <flux:error name="tc_agreed" />

                {{-- Submit --}}
                <flux:button type="submit" variant="filled"
                    class="w-full bg-amber-500 text-white hover:bg-amber-600 font-bold text-base py-3"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>★ Join NGN Club for Free</span>
                    <span wire:loading>Submitting…</span>
                </flux:button>

                <p class="text-xs text-gray-400 text-center">
                    Your passkey will be sent by SMS once your membership is confirmed. No card required.
                </p>

            </form>

        </flux:card>
    @endif

</div>

</div>
