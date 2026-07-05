<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-2xl">🔔</div>
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">MOT / Tax alert</h3>
            <p class="text-xs text-slate-600 dark:text-gray-400">Never miss your MOT or tax renewal</p>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit="submitAlert" class="site-form site-form-stack">
        <x-site.form-grid :cols="2">
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
        </x-site.form-grid>

        <flux:field>
            <flux:label>Email *</flux:label>
            <flux:input wire:model="email" type="email" />
            <flux:error name="email" />
        </flux:field>

        <flux:field class="site-form-field-plate">
            <flux:label>Registration Number *</flux:label>
            <flux:input
                wire:model="regNo"
                placeholder="AB12 CDE"
                class="uppercase tracking-widest text-xl"
            />
            <flux:error name="regNo" />
        </flux:field>

        <flux:field>
            <flux:label>Phone Number *</flux:label>
            <flux:input wire:model="phone" type="tel" />
            <flux:error name="phone" />
        </flux:field>

        <div class="site-form-stack">
            <label class="site-form-consent">
                <input type="checkbox" wire:model="notifyEmail"> Notify by Email
            </label>
            <label class="site-form-consent">
                <input type="checkbox" wire:model="notifyPhone"> Notify by SMS
            </label>
            <label class="site-form-consent">
                <input type="checkbox" wire:model="enableDeals"> Opt in for exclusive deals & discounts
            </label>
        </div>

        <flux:button type="submit" variant="filled" size="base" class="w-full bg-brand-green text-white hover:bg-brand-green-dark hover:text-white">
            Subscribe to MOT/Tax Alerts
        </flux:button>

        <p class="text-xs text-slate-600 dark:text-gray-500 text-center">
            Unsubscribe anytime by emailing your reg & "unsubscribe" to
            <a href="mailto:customerservice@neguinhomotors.co.uk" class="text-brand-green dark:text-emerald-400 hover:underline">customerservice@neguinhomotors.co.uk</a>
        </p>
    </form>
</div>
