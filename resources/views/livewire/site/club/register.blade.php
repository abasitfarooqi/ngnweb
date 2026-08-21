<div>

{{-- Hero --}}
<div class="site-page-hero py-12">
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
        <x-site.form-panel title="Your Details">
            <form wire:submit="joinClub" class="site-form site-form-stack">
                @if($referralAccepted)
                    <flux:callout variant="success" icon="check-circle">
                        <flux:callout.text>Referral accepted.</flux:callout.text>
                    </flux:callout>
                @endif

                <flux:field>
                    <flux:label>Full Name *</flux:label>
                    <flux:input wire:model="full_name" placeholder="John Smith" autocomplete="name" />
                    <flux:error name="full_name" />
                </flux:field>

                <x-site.form-grid :cols="2">
                    <flux:field>
                        <flux:label>Email Address *</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="john@example.com" autocomplete="email" />
                        <flux:error name="email" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Phone Number *</flux:label>
                        <flux:input wire:model.live="phone" type="tel" inputmode="numeric" maxlength="11" placeholder="07123456789" autocomplete="tel" />
                        <flux:error name="phone" />
                        <flux:description>UK mobile only — 11 digits starting with 07. Typing 44… converts to 07… automatically. No + or symbols.</flux:description>
                    </flux:field>
                </x-site.form-grid>

                <flux:separator />

                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Your Motorbike <span class="font-normal text-gray-400">(optional)</span></p>

                    <flux:field class="site-form-field-plate mb-4">
                        <flux:label>Registration Number</flux:label>
                        <flux:input wire:model.live="vrm" placeholder="AB12CDE" maxlength="12" class="uppercase tracking-widest text-base" />
                        <flux:error name="vrm" />
                    </flux:field>

                    <x-site.form-grid :cols="3" :compact="true">
                        <flux:field>
                            <flux:label>Make</flux:label>
                            <flux:input wire:model.live="make" placeholder="Honda" maxlength="50" />
                            <flux:error name="make" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Model</flux:label>
                            <flux:input wire:model.live="model" placeholder="PCX 125" maxlength="50" />
                            <flux:error name="model" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Year</flux:label>
                            <flux:input wire:model.live="year" placeholder="2022" maxlength="4" inputmode="numeric" />
                            <flux:error name="year" />
                        </flux:field>
                    </x-site.form-grid>
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
                <label class="site-form-consent cursor-pointer">
                    <input type="checkbox" wire:model="tc_agreed">
                    <span>
                        I agree to the
                        <a href="{{ route('ngnclub.terms') }}" target="_blank" class="text-brand-green hover:underline font-medium">Terms &amp; Conditions</a>
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
        </x-site.form-panel>
    @endif

</div>

</div>
