<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Profile</h1>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit="save" class="space-y-6">

        {{-- Contact Details --}}
        <flux:card class="p-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Contact Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input value="{{ auth('customer')->user()->email }}" disabled class="bg-gray-100 dark:bg-gray-800" />
                    <flux:description>Email cannot be changed here</flux:description>
                </flux:field>
                <flux:field>
                    <flux:label>Phone *</flux:label>
                    <flux:input wire:model="phone" type="tel" />
                    <flux:error name="phone" />
                </flux:field>
                <flux:field>
                    <flux:label>WhatsApp</flux:label>
                    <flux:input wire:model="whatsapp" type="tel" />
                </flux:field>
                <flux:field>
                    <flux:label>Preferred Branch</flux:label>
                    <flux:select wire:model="preferred_branch_id">
                        <option value="">Select a branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>
        </flux:card>

        {{-- Identity Details --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Identity Details</h2>
                @if($profile && $profile->verification_status === 'verified')
                    <flux:badge color="green" class="text-xs">Verified</flux:badge>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>First Name</flux:label>
                    <flux:input wire:model="first_name" :disabled="$profile && $profile->isFieldLocked('first_name')" />
                    @if($profile && $profile->isFieldLocked('first_name'))
                        <flux:description>🔒 Locked – contact support to change</flux:description>
                    @endif
                    <flux:error name="first_name" />
                </flux:field>
                <flux:field>
                    <flux:label>Last Name</flux:label>
                    <flux:input wire:model="last_name" :disabled="$profile && $profile->isFieldLocked('last_name')" />
                    <flux:error name="last_name" />
                </flux:field>
                <flux:field>
                    <flux:label>Date of Birth *</flux:label>
                    <flux:input wire:model="dob" type="date" :disabled="$profile && $profile->isFieldLocked('dob')" />
                    <flux:error name="dob" />
                </flux:field>
                <flux:field>
                    <flux:label>Nationality</flux:label>
                    <flux:input wire:model="nationality" :disabled="$profile && $profile->isFieldLocked('nationality')" />
                </flux:field>
                <flux:field>
                    <flux:label>Postcode</flux:label>
                    <flux:input wire:model="postcode" />
                </flux:field>
                <flux:field>
                    <flux:label>City</flux:label>
                    <flux:input wire:model="city" />
                </flux:field>
                <flux:field>
                    <flux:label>Country</flux:label>
                    <flux:input wire:model="country" />
                </flux:field>
            </div>
            <div class="mt-4">
                <flux:field>
                    <flux:label>Address *</flux:label>
                    <flux:textarea wire:model="address" rows="2" :disabled="$profile && $profile->isFieldLocked('address')" />
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Licence Number *</flux:label>
                    <flux:input wire:model="license_number" :disabled="$profile && $profile->isFieldLocked('license_number')" />
                    @if($profile && $profile->isFieldLocked('license_number'))
                        <flux:description>🔒 Locked – contact support to change</flux:description>
                    @endif
                    <flux:error name="license_number" />
                </flux:field>
                <flux:field>
                    <flux:label>Issuing Country</flux:label>
                    <flux:input wire:model="license_issuance_authority" placeholder="UNITED KINGDOM" :disabled="$profile && $profile->isFieldLocked('license_number')" />
                </flux:field>
                <flux:field>
                    <flux:label>Issue Date *</flux:label>
                    <flux:input wire:model="license_issuance_date" type="date" :disabled="$profile && $profile->isFieldLocked('license_number')" />
                </flux:field>
                <flux:field>
                    <flux:label>Expiry Date *</flux:label>
                    <flux:input wire:model="license_expiry_date" type="date" :disabled="$profile && $profile->isFieldLocked('license_number')" />
                </flux:field>
            </div>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="emergency_contact_name" />
                </flux:field>
                <flux:field>
                    <flux:label>Phone</flux:label>
                    <flux:input wire:model="emergency_contact_phone" type="tel" />
                </flux:field>
                <flux:field>
                    <flux:label>Relationship</flux:label>
                    <flux:input wire:model="emergency_contact_relationship" />
                </flux:field>
            </div>
        </flux:card>

        <div class="flex justify-end">
            <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark px-8">
                Save Changes
            </flux:button>
        </div>
    </form>
</div>
