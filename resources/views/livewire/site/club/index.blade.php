<div>
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-amber-400 text-4xl mb-2">★</p>
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Join NGN Club</h1>
        <p class="text-gray-300 text-lg">Exclusive rewards, discounts & early access for loyal members</p>
    </div>
</div>

{{-- Benefits --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Member Benefits</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach([
            ['icon'=>'tag', 'title'=>'Exclusive Discounts', 'text'=>'Members get priority pricing on servicing, MOTs and accessories.'],
            ['icon'=>'bell', 'title'=>'MOT & Tax Alerts', 'text'=>'We remind you before your MOT or tax expires – never miss a deadline.'],
            ['icon'=>'star', 'title'=>'Loyalty Points', 'text'=>'Earn points on every purchase and service. Redeem for discounts.'],
            ['icon'=>'gift', 'title'=>'Birthday Offers', 'text'=>'Special treat waiting for you on your birthday.'],
            ['icon'=>'users', 'title'=>'Referral Rewards', 'text'=>'Refer a friend and both get rewarded when they join.'],
            ['icon'=>'bolt', 'title'=>'Priority Booking', 'text'=>'Members jump the queue for MOT slots and service bookings.'],
        ] as $item)
            <flux:card class="p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                        <flux:icon name="{{ $item['icon'] }}" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm mb-1">{{ $item['title'] }}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $item['text'] }}</p>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>
</div>

{{-- Join form --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-14">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 text-center">Join for Free</h2>
        <p class="text-sm text-gray-500 text-center mb-8">It takes 2 minutes. No card required.</p>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-5">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:card class="p-8">
            <form wire:submit="joinClub" class="space-y-4">
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
                <flux:field>
                    <flux:label>Motorcycle Registration (optional)</flux:label>
                    <flux:input wire:model="regNo" placeholder="AB12 CDE" class="uppercase tracking-wider font-bold bg-yellow-100 border-yellow-400" />
                </flux:field>
                <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <input type="checkbox" wire:model="consent" class="mt-0.5 accent-brand-red" />
                    <span>I agree to receive NGN Club communications & marketing emails *</span>
                </label>
                <flux:error name="consent" />
                <flux:button type="submit" variant="filled" size="base" class="w-full bg-amber-500 text-white hover:bg-amber-600 font-semibold">
                    ★ Join NGN Club
                </flux:button>
            </form>
        </flux:card>
    </div>
</div>
</div>
