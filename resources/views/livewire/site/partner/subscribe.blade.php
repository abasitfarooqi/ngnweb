<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">Join NGN Partner Network</h1>
        <p class="text-gray-300 text-lg">Grow your business with NGN Motors</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-6">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:card class="p-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Partner Registration</h2>

        <form wire:submit="register" class="space-y-5" enctype="multipart/form-data">

            {{-- Company details --}}
            <flux:field>
                <flux:label>Company Name *</flux:label>
                <flux:input wire:model="companyname" placeholder="Your company name" />
                <flux:error name="companyname" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Company Registration No.</flux:label>
                    <flux:input wire:model="company_number" placeholder="12345678" />
                    <flux:error name="company_number" />
                </flux:field>
                <flux:field>
                    <flux:label>Fleet Size</flux:label>
                    <flux:input wire:model="fleet_size" type="number" min="0" placeholder="Number of vehicles" />
                    <flux:error name="fleet_size" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Company Address</flux:label>
                <flux:input wire:model="company_address" placeholder="Street, city, postcode" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Website</flux:label>
                    <flux:input wire:model="website" type="url" placeholder="https://example.com" />
                </flux:field>
                <flux:field>
                    <flux:label>Operating Since</flux:label>
                    <flux:input wire:model="operating_since" placeholder="YYYY" maxlength="8" />
                </flux:field>
            </div>

            {{-- Contact person --}}
            <flux:separator text="Contact Person" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>First Name</flux:label>
                    <flux:input wire:model="first_name" />
                </flux:field>
                <flux:field>
                    <flux:label>Last Name</flux:label>
                    <flux:input wire:model="last_name" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Phone</flux:label>
                    <flux:input wire:model="phone" type="tel" />
                </flux:field>
                <flux:field>
                    <flux:label>Mobile</flux:label>
                    <flux:input wire:model="mobile" type="tel" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="email" type="email" />
                <flux:error name="email" />
            </flux:field>

            {{-- Logo upload --}}
            <flux:field>
                <flux:label>Company Logo (optional)</flux:label>
                <input type="file" wire:model="company_logo" accept="image/*"
                       class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                <flux:error name="company_logo" />
            </flux:field>

            {{-- T&C --}}
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" wire:model="tc_agreed" class="mt-0.5 w-4 h-4 accent-brand-red flex-shrink-0" />
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    I agree to the
                    <a href="{{ route('ngnpartner.terms') }}" target="_blank" class="text-brand-red hover:underline font-medium">Partner Terms & Conditions</a> *
                </span>
            </label>
            <flux:error name="tc_agreed" />

            <flux:button type="submit" variant="filled" size="base"
                class="w-full bg-brand-red text-white hover:bg-brand-red-dark"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Register as Partner</span>
                <span wire:loading>Submitting…</span>
            </flux:button>
        </form>
    </flux:card>
</div>
</div>
