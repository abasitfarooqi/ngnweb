<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-2xl">🔔</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">MOT / Tax Alert</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Never miss your MOT or tax renewal</p>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit="submitAlert" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>First Name *</flux:label>
                <flux:input wire:model="firstName" class="uppercase" />
                <flux:error name="firstName" />
            </flux:field>
            <flux:field>
                <flux:label>Last Name *</flux:label>
                <flux:input wire:model="lastName" class="uppercase" />
                <flux:error name="lastName" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Email *</flux:label>
            <flux:input wire:model="email" type="email" />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Registration Number *</flux:label>
            <flux:input
                wire:model="regNo"
                placeholder="AB12 CDE"
                class="uppercase tracking-widest font-bold text-center text-xl bg-yellow-100 border-2 border-yellow-400 text-black"
            />
            <flux:error name="regNo" />
        </flux:field>

        <flux:field>
            <flux:label>Phone Number *</flux:label>
            <flux:input wire:model="phone" type="tel" />
            <flux:error name="phone" />
        </flux:field>

        <div class="space-y-2 pt-1">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" wire:model="notifyEmail" class="accent-brand-red"> Notify by Email
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" wire:model="notifyPhone" class="accent-brand-red"> Notify by SMS
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" wire:model="enableDeals" class="accent-brand-red"> Opt in for exclusive deals & discounts
            </label>
        </div>

        <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
            Subscribe to MOT/Tax Alerts
        </flux:button>

        <p class="text-xs text-gray-500 text-center">
            Unsubscribe anytime by emailing your reg & "unsubscribe" to
            <a href="mailto:customerservice@neguinhomotors.co.uk" class="text-brand-red hover:underline">customerservice@neguinhomotors.co.uk</a>
        </p>
    </form>
</div>
