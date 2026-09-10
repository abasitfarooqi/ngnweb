<div>
    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.customers.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Customers</a>
                <span>/</span>
                <span>{{ method_exists($this, 'isPortalUserEditor') ? 'Portal users / Edit' : ($customer && $customer->exists ? 'Edit ' . $customer->full_name : 'New Customer') }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ method_exists($this, 'isPortalUserEditor') ? 'Edit portal user — ' . $customer->full_name : ($customer && $customer->exists ? 'Edit ' . $customer->full_name : 'New Customer') }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ method_exists($this, 'isPortalUserEditor') ? route('flux-admin.portal-users.index') : route('flux-admin.customers.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save customer</flux:button>
        </div>
    </div>

    @if(method_exists($this, 'isPortalUserEditor') && $customer && $customer->exists)
        <div class="mb-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-start gap-3">
                <flux:icon name="shield-check" class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                <div class="min-w-0 flex-1">
                    <h2 class="font-semibold text-zinc-900 dark:text-white">Restricted portal record</h2>
                    <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div><div class="text-xs text-zinc-500 dark:text-zinc-400">Customer record</div><div class="font-semibold text-zinc-900 dark:text-white">#{{ $customer->id }}</div></div>
                        <div><div class="text-xs text-zinc-500 dark:text-zinc-400">Portal auth record</div><div class="font-semibold text-zinc-900 dark:text-white">{{ $customer->customerAuth?->id ? '#'.$customer->customerAuth->id : 'Not created' }}</div></div>
                        <div><div class="text-xs text-zinc-500 dark:text-zinc-400">Password</div><div class="font-semibold text-zinc-900 dark:text-white">{{ $customer->customerAuth?->password ? 'Set · hidden' : 'Not set' }}</div></div>
                        <div><div class="text-xs text-zinc-500 dark:text-zinc-400">Email verification</div><div class="font-semibold text-zinc-900 dark:text-white">{{ $customer->customerAuth?->email_verified_at?->format('d M Y H:i') ?: 'Not verified' }}</div></div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <flux:button type="button" size="sm" variant="ghost" icon="link" wire:click="sendPortalResetLink('email')" class="rounded-xl">Send reset link by email</flux:button>
                        <flux:button type="button" size="sm" variant="ghost" icon="device-phone-mobile" wire:click="sendPortalResetLink('sms')" class="rounded-xl">Send reset link by SMS</flux:button>
                    </div>
                    @if(method_exists($this, 'isPortalUserEditor'))
                        <div class="mt-4 grid max-w-2xl gap-2 sm:grid-cols-[1fr_1fr_auto]">
                            <flux:input type="password" wire:model="portalPassword" placeholder="Set new portal password" autocomplete="new-password" />
                            <flux:input type="password" wire:model="portalPassword_confirmation" placeholder="Confirm password" autocomplete="new-password" />
                            <flux:button type="button" size="sm" variant="primary" wire:click="setPortalPassword" class="rounded-xl">Save password</flux:button>
                        </div>
                        @error('portalPassword') <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</div> @enderror
                    @endif
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6" novalidate>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Personal details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="First name" required :error="$errors->first('form.first_name')">
                    <flux:input wire:model="form.first_name" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Last name" required :error="$errors->first('form.last_name')">
                    <flux:input wire:model="form.last_name" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Date of birth" :error="$errors->first('form.dob')">
                    <flux:input type="date" wire:model="form.dob" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Email" :error="$errors->first('form.email')">
                    <flux:input type="email" wire:model="form.email" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Phone" :error="$errors->first('form.phone')">
                    <flux:input wire:model="form.phone" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="WhatsApp" :error="$errors->first('form.whatsapp')">
                    <flux:input wire:model="form.whatsapp" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Address" :error="$errors->first('form.address')">
                    <flux:input wire:model="form.address" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Postcode" :error="$errors->first('form.postcode')">
                    <flux:input wire:model="form.postcode" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="City" :error="$errors->first('form.city')">
                    <flux:input wire:model="form.city" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Country" :error="$errors->first('form.country')">
                    <flux:input wire:model="form.country" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Nationality" :error="$errors->first('form.nationality')">
                    <flux:input wire:model="form.nationality" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Emergency contact" :error="$errors->first('form.emergency_contact')">
                    <flux:input wire:model="form.emergency_contact" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Licence details</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Licence number" :error="$errors->first('form.license_number')">
                    <flux:input wire:model="form.license_number" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Licence issued (date)" :error="$errors->first('form.license_issuance_date')">
                    <flux:input type="date" wire:model="form.license_issuance_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Licence expiry" :error="$errors->first('form.license_expiry_date')">
                    <flux:input type="date" wire:model="form.license_expiry_date" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Licence issuance country" :error="$errors->first('form.license_issuance_authority')">
                    <flux:input wire:model="form.license_issuance_authority" placeholder="Country where licence was issued" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Account settings</h2>

            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Star rating (1–5)" :error="$errors->first('form.rating')">
                    <flux:input type="number" min="1" max="5" step="1" wire:model="form.rating" />
                </x-flux-admin::field-group>

                <x-flux-admin::field-group label="Preferred branch" :error="$errors->first('form.preferred_branch_id')">
                    <flux:select wire:model="form.preferred_branch_id">
                        <flux:select.option value="">— None —</flux:select.option>
                        @foreach($branches as $branch)
                            <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <div class="mt-4">
                <x-flux-admin::field-group label="Internal note" :error="$errors->first('form.reputation_note')">
                    <flux:textarea wire:model="form.reputation_note" rows="3" placeholder="Internal notes about this customer (not visible to customer)" />
                </x-flux-admin::field-group>
            </div>

            @if($customer && $customer->exists)
                <div class="mt-5 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-2">Customer portal controls</h3>
                    @if(\App\Support\FluxAdminAccess::canManagePortalUsers())
                        <div class="mb-4 flex flex-wrap gap-2 text-sm">
                            <span class="rounded px-2 py-1 {{ ($form['is_active'] ?? true) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ($form['is_active'] ?? true) ? '● Customer active' : '● Customer inactive' }}</span>
                            <span class="rounded px-2 py-1 {{ $customer->customerAuth?->is_active && ($form['is_register'] ?? $customer->is_register) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $customer->customerAuth?->is_active && ($form['is_register'] ?? $customer->is_register) ? '● Portal active' : '● Portal inactive' }}</span>
                            <span class="rounded px-2 py-1 {{ $customer->customerAuth?->hasVerifiedEmail() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $customer->customerAuth?->hasVerifiedEmail() ? '✓ Email verified' : '✕ Email not verified' }}</span>
                            <span class="rounded px-2 py-1 {{ ($form['portal_upload_access'] ?? false) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ($form['portal_upload_access'] ?? false) ? '✓ Uploads allowed' : '✕ Uploads disabled' }}</span>
                        </div>
                        <div class="mb-4 flex flex-wrap gap-2">
                            <flux:button type="button" size="sm" variant="ghost" wire:click="toggleCustomerActive">{{ ($form['is_active'] ?? true) ? 'Deactivate customer' : 'Activate customer' }}</flux:button>
                            <flux:button type="button" size="sm" variant="ghost" wire:click="togglePortalActive">{{ $customer->customerAuth?->is_active ? 'Deactivate portal' : 'Activate portal' }}</flux:button>
                            <flux:button type="button" size="sm" variant="ghost" wire:click="togglePortalUploadAccess">{{ ($form['portal_upload_access'] ?? false) ? 'Disable uploads' : 'Allow uploads' }}</flux:button>
                            <flux:button type="button" size="sm" variant="primary" wire:click="sendPortalCredentials('email')">Send credentials by email</flux:button>
                            <flux:button type="button" size="sm" variant="primary" wire:click="sendPortalCredentials('sms')">Send credentials by SMS</flux:button>
                        </div>
                    @endif
                    <p class="text-xs text-zinc-500 mb-3">
                        Profile initialised: {{ $customer->profile_initialised_at?->format('d M Y H:i') ?? 'Not yet' }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @if($customer->profile_initialised_at && ! ($form['profile_editing_unlocked'] ?? false))
                            <flux:button type="button" size="sm" variant="primary" wire:click="setProfileEditingUnlocked(true)" class="!rounded-none">Unlock profile editing</flux:button>
                        @elseif($form['profile_editing_unlocked'] ?? false)
                            <flux:button type="button" size="sm" variant="ghost" wire:click="setProfileEditingUnlocked(false)" class="!rounded-none">Lock profile editing</flux:button>
                        @endif
                        @if(! ($form['document_reupload_unlocked'] ?? false))
                            <flux:button type="button" size="sm" variant="primary" wire:click="setDocumentReuploadUnlocked(true)" class="!rounded-none">Allow approved doc re-upload</flux:button>
                        @else
                            <flux:button type="button" size="sm" variant="ghost" wire:click="setDocumentReuploadUnlocked(false)" class="!rounded-none">Lock approved doc re-upload</flux:button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.customers.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save customer</flux:button>
        </div>

    </form>
</div>
