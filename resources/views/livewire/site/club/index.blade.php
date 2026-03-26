<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-amber-400 text-3xl mb-2">★</p>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">NGN Club</h1>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto">Exclusive rewards, discounts &amp; early access for loyal members across all three London branches.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            @if($loggedInMember)
                <a href="{{ route('ngnclub.dashboard') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 text-white font-semibold hover:bg-amber-600 transition">
                    ★ My Dashboard
                </a>
            @else
                <a href="{{ route('ngnclub.login') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-brand-red text-white font-semibold hover:bg-red-700 transition">
                    Member Login
                </a>
                <a href="#join-form"
                   class="inline-flex items-center gap-2 px-6 py-3 border border-amber-500 text-amber-400 font-semibold hover:bg-amber-500 hover:text-white transition">
                    ★ Join for Free
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Benefits --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-10 text-center">Why Join NGN Club?</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach([
            ['icon'=>'tag', 'title'=>'Exclusive Discounts', 'text'=>'Members get priority pricing on servicing, MOTs and accessories at all three branches.'],
            ['icon'=>'bell', 'title'=>'MOT & Tax Reminders', 'text'=>'We remind you before your MOT or tax expires — never miss a deadline again.'],
            ['icon'=>'star', 'title'=>'Loyalty Rewards', 'text'=>'Earn cashback rewards on every purchase and service. Redeem at any NGN branch.'],
            ['icon'=>'gift', 'title'=>'Birthday Treat', 'text'=>'A special offer waiting for you on your birthday — just our way of saying thanks.'],
            ['icon'=>'users', 'title'=>'Refer & Earn', 'text'=>'Refer a friend and you both get rewarded when they join or make their first visit.'],
            ['icon'=>'bolt', 'title'=>'Priority Booking', 'text'=>'Members get priority access to MOT slots, service bookings and new stock alerts.'],
        ] as $item)
            <flux:card class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                        <flux:icon name="{{ $item['icon'] }}" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item['text'] }}</p>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Already logged in banner --}}
@if($loggedInMember)
    <div class="bg-amber-50 dark:bg-amber-900/10 border-y border-amber-200 dark:border-amber-800 py-10 text-center">
        <p class="text-amber-700 dark:text-amber-400 font-semibold mb-3">You are already an NGN Club member, {{ $loggedInMember->full_name }}!</p>
        <a href="{{ route('ngnclub.dashboard') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 text-white font-semibold hover:bg-amber-600 transition">
            ★ Go to My Dashboard
        </a>
    </div>
@else
    {{-- Join form --}}
    <div id="join-form" class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 text-center">Join for Free</h2>
            <p class="text-sm text-gray-500 text-center mb-8">Takes 2 minutes. No card required. Passkey sent via SMS.</p>

            @if(session('success'))
                <flux:callout variant="success" icon="check-circle" class="mb-6">
                    <flux:callout.text>{{ session('success') }}</flux:callout.text>
                </flux:callout>
            @endif

            <flux:card class="p-8">
                <form wire:submit="joinClub" class="space-y-5">
                    <flux:field>
                        <flux:label>Full Name *</flux:label>
                        <flux:input wire:model="full_name" placeholder="John Smith" />
                        <flux:error name="full_name" />
                    </flux:field>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Email *</flux:label>
                            <flux:input wire:model="email" type="email" placeholder="john@example.com" />
                            <flux:error name="email" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Phone Number *</flux:label>
                            <flux:input wire:model="phone" type="tel" placeholder="07700 900 123" />
                            <flux:error name="phone" />
                        </flux:field>
                    </div>

                    <flux:separator />

                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Your Motorbike (optional)</p>

                    <flux:field>
                        <flux:label>Registration Number</flux:label>
                        <flux:input wire:model="vrm" placeholder="AB12 CDE" class="uppercase tracking-widest font-bold bg-yellow-50 dark:bg-yellow-900/10 border-yellow-400" />
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
                        </flux:field>
                    </div>

                    <flux:separator />

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="tc_agreed" class="mt-0.5 accent-brand-red w-4 h-4" />
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            I agree to the
                            <a href="{{ route('ngnclub.terms') }}" target="_blank" class="text-brand-red hover:underline">NGN Club Terms &amp; Conditions</a>
                            and to receive club communications *
                        </span>
                    </label>
                    <flux:error name="tc_agreed" />

                    <flux:button type="submit" variant="filled" class="w-full bg-amber-500 text-white hover:bg-amber-600 font-semibold text-base py-3" wire:loading.attr="disabled">
                        <span wire:loading.remove>★ Join NGN Club</span>
                        <span wire:loading>Submitting…</span>
                    </flux:button>

                    <p class="text-center text-sm text-gray-500">
                        Already a member?
                        <a href="{{ route('ngnclub.login') }}" class="text-brand-red hover:underline">Login here</a>
                    </p>
                </form>
            </flux:card>
        </div>
    </div>
@endif

{{-- How it works --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 dark:border-gray-800">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-10 text-center">How It Works</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
        @foreach([
            ['step'=>'1', 'title'=>'Join Free', 'text'=>'Fill in the form above. It takes under 2 minutes.'],
            ['step'=>'2', 'title'=>'Earn Rewards', 'text'=>'Every time you visit NGN, your spend earns cashback rewards.'],
            ['step'=>'3', 'title'=>'Redeem Anytime', 'text'=>'Use your accumulated credit towards future visits and services.'],
        ] as $step)
            <div>
                <div class="w-12 h-12 bg-brand-red text-white font-black text-xl flex items-center justify-center mx-auto mb-4">
                    {{ $step['step'] }}
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $step['text'] }}</p>
            </div>
        @endforeach
    </div>
</div>

</div>
