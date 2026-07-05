<div>
{{-- Hero --}}
<div class="site-page-hero bg-gradient-to-r from-brand-green to-emerald-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Request a Call Back</h1>
        <p class="text-xl text-emerald-100">We'll call you at your convenience</p>
    </div>
</div>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <x-site.form-panel title="Call Back Request Form">
        <form wire:submit.prevent="submitRequest" class="site-form site-form-stack">
            <flux:field>
                <flux:label>Full Name *</flux:label>
                <flux:input wire:model="name" type="text" />
                <flux:error name="name" />
            </flux:field>

            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>Phone Number *</flux:label>
                    <flux:input wire:model="phone" type="tel" />
                    <flux:error name="phone" />
                </flux:field>
                <flux:field>
                    <flux:label>Email (optional)</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="we'll reply here if provided" />
                    <flux:error name="email" />
                </flux:field>
            </x-site.form-grid>

            <flux:field>
                <flux:label>Preferred Time *</flux:label>
                <flux:select wire:model="preferredTime" variant="listbox" placeholder="Select time...">
                    <flux:select.option value="morning">Morning (9AM – 12PM)</flux:select.option>
                    <flux:select.option value="afternoon">Afternoon (12PM – 5PM)</flux:select.option>
                    <flux:select.option value="evening">Evening (5PM – 7PM)</flux:select.option>
                    <flux:select.option value="anytime">Any time</flux:select.option>
                </flux:select>
                <flux:error name="preferredTime" />
            </flux:field>

            <flux:field>
                <flux:label>Message (Optional)</flux:label>
                <flux:textarea wire:model="message" rows="5" placeholder="Tell us briefly what you need help with..." />
            </flux:field>

            <flux:button type="submit" variant="filled" class="w-full bg-brand-green text-white hover:bg-brand-green-dark">Request Call Back</flux:button>
        </form>
    </x-site.form-panel>
</div>
