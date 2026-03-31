<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Security Settings</h1>

    @if(request()->boolean('verified'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>Your email address is verified.</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('status') === 'verification-link-sent')
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>A new verification link has been sent to your email address.</flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(session('error'))
        <flux:callout variant="danger" icon="exclamation-triangle" class="mb-5">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Password Change --}}
    <flux:card class="p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Change Password</h2>
        @if($canChangePassword)
            <form wire:submit="updatePassword" class="space-y-4 max-w-sm">
                <flux:field>
                    <flux:label>Current Password</flux:label>
                    <flux:input wire:model="current_password" type="password" />
                    <flux:error name="current_password" />
                </flux:field>
                <flux:field>
                    <flux:label>New Password</flux:label>
                    <flux:input wire:model="password" type="password" />
                    <flux:error name="password" />
                </flux:field>
                <flux:field>
                    <flux:label>Confirm New Password</flux:label>
                    <flux:input wire:model="password_confirmation" type="password" />
                </flux:field>
                <flux:button type="submit" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
                    Update Password
                </flux:button>
            </form>
        @else
            <flux:callout variant="warning" icon="information-circle">
                <flux:callout.text>
                    Password changes in the portal are available only for registered club members. Passwords are stored securely and cannot be shown here; use the form above once your account is eligible.
                </flux:callout.text>
            </flux:callout>
        @endif
    </flux:card>

    @if($clubMember)
        <flux:card class="p-6 mb-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-2">NGN Club (read only)</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Linked club membership details. Club login uses this email with the password you set for your portal account when you are a registered club member.
            </p>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Club member ID</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $clubMember->id }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email on club record</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $clubMember->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Phone on club record</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $clubMember->phone }}</dd>
                </div>
            </dl>
        </flux:card>
    @endif

    {{-- Two-Factor Authentication --}}
    <flux:card class="p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 dark:text-white mb-2">Two-Factor Authentication</h2>
        <flux:callout variant="info" icon="information-circle">
            <flux:callout.text>
                Two-factor authentication for customer accounts is coming soon.
                For account security concerns, please <a href="{{ route('site.contact') }}" class="underline font-medium">contact us</a>.
            </flux:callout.text>
        </flux:callout>
    </flux:card>

    {{-- Email Verification --}}
    <flux:card class="p-6">
        <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Email Verification</h2>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Email: <span class="font-medium">{{ $user->email }}</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Status:
                    @if ($user->hasVerifiedEmail())
                        <span class="text-green-600 dark:text-green-400">✓ Verified</span>
                    @else
                        <span class="text-yellow-600 dark:text-yellow-400">Not verified</span>
                    @endif
                </p>
                <flux:error name="verification" />
            </div>

            @if (!$user->hasVerifiedEmail())
                <flux:button type="button" wire:click="resendVerificationEmail" wire:loading.attr="disabled" variant="filled" class="bg-brand-red text-white shrink-0">
                    <span wire:loading.remove wire:target="resendVerificationEmail">Resend verification email</span>
                    <span wire:loading wire:target="resendVerificationEmail">Sending…</span>
                </flux:button>
            @endif
        </div>
    </flux:card>
</div>
