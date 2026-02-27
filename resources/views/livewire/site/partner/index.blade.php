<div>
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">Partner Programme</h1>
        <p class="text-gray-300 text-lg">Earn money by referring customers to NGN Motors</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 text-center">
        @foreach([
            ['num'=>'1', 'title'=>'Sign Up', 'text'=>'Register as a partner below.'],
            ['num'=>'2', 'title'=>'Refer Customers', 'text'=>'Share your unique referral code with riders.'],
            ['num'=>'3', 'title'=>'Earn Rewards', 'text'=>'Get paid for every successful referral.'],
        ] as $step)
            <div>
                <div class="w-14 h-14 bg-brand-red text-white flex items-center justify-center text-2xl font-bold mx-auto mb-3">{{ $step['num'] }}</div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $step['text'] }}</p>
            </div>
        @endforeach
    </div>

    <flux:card class="p-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Register as a Partner</h2>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-5">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        <form wire:submit="register" class="space-y-4">
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
                <flux:label>Phone</flux:label>
                <flux:input wire:model="phone" type="tel" />
            </flux:field>
            <flux:field>
                <flux:label>Company / Business Name (if applicable)</flux:label>
                <flux:input wire:model="company" />
            </flux:field>
            <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                Register as Partner
            </flux:button>
        </form>
    </flux:card>
</div>
</div>
