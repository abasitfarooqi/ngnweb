<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Profile</h1>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-5">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    @php $canEditPortal = $profile && $profile->canCustomerEditPortal(); @endphp

    @if(! $canEditPortal)
        <flux:callout variant="warning" icon="lock-closed" class="mb-5">
            <flux:callout.text>
                Your account is read-only until NGN authorises profile editing. You can view your details below but cannot change them yet. Contact us if you need access.
            </flux:callout.text>
        </flux:callout>
    @elseif($profile && $profile->profile_initialised_at)
        <flux:callout variant="info" icon="information-circle" class="mb-5">
            <flux:callout.text>
                After you save, identity details lock again until NGN unlocks editing for further changes.
            </flux:callout.text>
        </flux:callout>
    @endif

    @php
        $identityDisabled = fn (string $field): bool => ! $canEditPortal || ($profile && $profile->isFieldLocked($field));
    @endphp

    <form wire:submit="save" class="site-form site-form-stack">
        <flux:card class="p-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Contact Details</h2>
            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input value="{{ auth('customer')->user()->email }}" disabled class="bg-gray-100 dark:bg-gray-800" />
                    <flux:description>Email cannot be changed here</flux:description>
                </flux:field>
                <flux:field>
                    <flux:label>Phone *</flux:label>
                    <flux:input wire:model="phone" type="tel" :disabled="! $canEditPortal" />
                    <flux:error name="phone" />
                </flux:field>
                <flux:field>
                    <flux:label>WhatsApp</flux:label>
                    <flux:input wire:model="whatsapp" type="tel" :disabled="! $canEditPortal" />
                </flux:field>
                <flux:field>
                    <flux:label>Preferred Branch</flux:label>
                    <flux:select wire:model="preferred_branch_id" variant="listbox" searchable placeholder="Select a branch" :disabled="! $canEditPortal">
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </x-site.form-grid>
        </flux:card>

        {{-- Identity Details --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Identity Details</h2>
                @if($profile && $profile->verification_status === 'verified')
                    <flux:badge color="green" class="text-xs">Verified</flux:badge>
                @endif
            </div>
            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>First Name</flux:label>
                    <flux:input wire:model="first_name" :disabled="$identityDisabled('first_name')" />
                    @if($identityDisabled('first_name'))
                        <flux:description>🔒 Locked – contact support to change</flux:description>
                    @endif
                    <flux:error name="first_name" />
                </flux:field>
                <flux:field>
                    <flux:label>Last Name</flux:label>
                    <flux:input wire:model="last_name" :disabled="$identityDisabled('last_name')" />
                    <flux:error name="last_name" />
                </flux:field>
                <flux:field>
                    <flux:label>Date of Birth *</flux:label>
                    <flux:date-picker wire:model="dob" :disabled="$identityDisabled('dob')" />
                    <flux:error name="dob" />
                </flux:field>
                <flux:field>
                    <flux:label>Nationality</flux:label>
                    <flux:input wire:model="nationality" :disabled="$identityDisabled('nationality')" />
                </flux:field>
                <flux:field>
                    <flux:label>Postcode</flux:label>
                    <flux:input wire:model="postcode" :disabled="! $canEditPortal" />
                </flux:field>
                <flux:field>
                    <flux:label>City</flux:label>
                    <flux:input wire:model="city" :disabled="! $canEditPortal" />
                </flux:field>
                <flux:field>
                    <flux:label>Country</flux:label>
                    <flux:input wire:model="country" :disabled="! $canEditPortal" />
                </flux:field>
            </x-site.form-grid>
            <div class="mt-4">
                <flux:field>
                    <flux:label>Address *</flux:label>
                    <flux:textarea wire:model="address" rows="2" :disabled="$identityDisabled('address')" />
                    <flux:error name="address" />
                </flux:field>
            </div>
        </flux:card>

        {{-- Driving Licence --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Driving Licence</h2>
                <span class="text-xs text-gray-500">Required for rentals</span>
            </div>
            <x-site.form-grid :cols="2">
                <flux:field>
                    <flux:label>Licence Number *</flux:label>
                    <flux:input wire:model="license_number" :disabled="$identityDisabled('license_number')" />
                    @if($identityDisabled('license_number'))
                        <flux:description>🔒 Locked – contact support to change</flux:description>
                    @endif
                    <flux:error name="license_number" />
                </flux:field>
                <flux:field>
                    <flux:label>Issuing Country</flux:label>
                    <flux:input wire:model="license_issuance_authority" placeholder="UNITED KINGDOM" :disabled="$identityDisabled('license_number')" />
                </flux:field>
                <flux:field>
                    <flux:label>Issue Date *</flux:label>
                    <flux:date-picker wire:model="license_issuance_date" :disabled="$identityDisabled('license_number')" />
                </flux:field>
                <flux:field>
                    <flux:label>Expiry Date *</flux:label>
                    <flux:date-picker wire:model="license_expiry_date" :disabled="$identityDisabled('license_number')" />
                </flux:field>
            </x-site.form-grid>
            <flux:callout variant="info" icon="document-text" class="mt-4">
                <flux:callout.text class="text-xs">
                    Please upload photos of your licence (front and back) in the
                    <a href="{{ route('account.documents') }}" class="underline">Documents</a> section.
                </flux:callout.text>
            </flux:callout>
        </flux:card>

        {{-- Emergency Contact --}}
        <flux:card class="p-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Emergency Contact</h2>
            <div class="grid grid-cols-1 gap-4">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="emergency_contact_name" :disabled="! $canEditPortal" />
                </flux:field>
            </div>
        </flux:card>

        @if($canEditPortal)
        <div class="flex justify-end">
            <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark px-8">
                Save Changes
            </flux:button>
        </div>
        @endif
    </form>
</div>
