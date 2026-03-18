<div class="space-y-6 max-w-3xl">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <flux:heading size="xl">My Addresses</flux:heading>
        <flux:button wire:click="openNew" icon="plus" variant="filled" class="bg-brand-red text-white hover:bg-red-700">
            Add Address
        </flux:button>
    </div>

    {{-- Success message --}}
    @if($successMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
             class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 text-sm">
            {{ $successMessage }}
        </div>
    @endif

    {{-- Add / Edit form --}}
    @if($showForm)
        <flux:card class="p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-5">
                {{ $editId ? 'Edit Address' : 'New Address' }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <flux:label>First Name</flux:label>
                    <flux:input wire:model="first_name" />
                    @error('first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:label>Last Name</flux:label>
                    <flux:input wire:model="last_name" />
                    @error('last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:label>Company (optional)</flux:label>
                    <flux:input wire:model="company_name" />
                </div>
                <div>
                    <flux:label>Phone Number</flux:label>
                    <flux:input wire:model="phone_number" type="tel" />
                    @error('phone_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <flux:label>Street Address</flux:label>
                    <flux:input wire:model="street_address" />
                    @error('street_address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <flux:label>Apt / Suite / Unit (optional)</flux:label>
                    <flux:input wire:model="street_address_plus" />
                </div>
                <div>
                    <flux:label>City / Town</flux:label>
                    <flux:input wire:model="city" />
                    @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:label>Postcode</flux:label>
                    <flux:input wire:model="postcode" />
                    @error('postcode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:label>Country</flux:label>
                    <flux:select wire:model="country_id">
                        @foreach($countries as $country)
                            <flux:select.option value="{{ $country->id }}">{{ $country->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:label>Type</flux:label>
                    <flux:select wire:model="type">
                        <flux:select.option value="shipping">Shipping</flux:select.option>
                        <flux:select.option value="billing">Billing</flux:select.option>
                    </flux:select>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <flux:button wire:click="save" variant="filled" class="bg-brand-red text-white hover:bg-red-700">
                    {{ $editId ? 'Update Address' : 'Save Address' }}
                </flux:button>
                <flux:button wire:click="cancel" variant="ghost">Cancel</flux:button>
            </div>
        </flux:card>
    @endif

    {{-- Address list --}}
    @if($addresses->isEmpty())
        <flux:card class="p-12 text-center">
            <flux:icon name="map-pin" class="h-12 w-12 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-600 dark:text-gray-400 mb-4">No addresses saved yet.</p>
            <flux:button wire:click="openNew" variant="filled" class="bg-brand-red text-white hover:bg-red-700">
                Add Your First Address
            </flux:button>
        </flux:card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($addresses as $address)
                <flux:card class="p-5 {{ $address->is_default ? 'border-brand-red border-2' : '' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $address->first_name }} {{ $address->last_name }}
                            </p>
                            @if($address->is_default)
                                <flux:badge color="red" size="sm" class="mt-1">Default</flux:badge>
                            @endif
                        </div>
                        <flux:badge color="{{ $address->type === 'billing' ? 'blue' : 'zinc' }}" size="sm">
                            {{ ucfirst($address->type) }}
                        </flux:badge>
                    </div>
                    <address class="not-italic text-sm text-gray-500 dark:text-gray-400 space-y-0.5 mb-4">
                        <p>{{ $address->street_address }}</p>
                        @if($address->street_address_plus && $address->street_address_plus !== '-')
                            <p>{{ $address->street_address_plus }}</p>
                        @endif
                        <p>{{ $address->city }}, {{ $address->postcode }}</p>
                        @if($address->phone_number) <p>{{ $address->phone_number }}</p> @endif
                    </address>
                    <div class="flex gap-2 flex-wrap">
                        <flux:button wire:click="edit({{ $address->id }})" size="sm" variant="outline">Edit</flux:button>
                        @if(!$address->is_default)
                            <flux:button wire:click="setDefault({{ $address->id }})" size="sm" variant="ghost">
                                Set Default
                            </flux:button>
                            <flux:button wire:click="delete({{ $address->id }})"
                                         wire:confirm="Remove this address?"
                                         size="sm" variant="ghost"
                                         class="text-red-500 hover:text-red-700">
                                Remove
                            </flux:button>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
