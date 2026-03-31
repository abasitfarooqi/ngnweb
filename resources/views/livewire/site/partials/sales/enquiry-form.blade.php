<div class="border border-gray-200 dark:border-gray-700 p-5 space-y-4">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order / Enquiry</h3>

    @if (session()->has('enquiry_success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.text>{{ session('enquiry_success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit.prevent="{{ $submitAction }}" class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <flux:field>
                <flux:label>Full name</flux:label>
                <flux:input wire:model.defer="name" />
                <flux:error name="name" />
            </flux:field>
            <flux:field>
                <flux:label>Phone</flux:label>
                <flux:input wire:model.defer="phone" />
                <flux:error name="phone" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input wire:model.defer="email" type="email" />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Message</flux:label>
            <flux:textarea wire:model.defer="message" rows="4" />
            <flux:error name="message" />
        </flux:field>

        <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" wire:model="privacy" class="mt-1">
            <span>I agree to be contacted about this enquiry.</span>
        </label>
        <flux:error name="privacy" />

        <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
            Submit Enquiry
        </flux:button>
    </form>
</div>
