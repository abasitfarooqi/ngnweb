<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Security Settings</h1>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Password Change --}}
    <flux:card class="p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Change Password</h2>
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
    </flux:card>

    {{-- Two-Factor Authentication --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Two-Factor Authentication</h2>
        
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Status: 
                    @if ($twoFactorEnabled)
                        <span class="font-medium text-green-600 dark:text-green-400">Enabled</span>
                    @else
                        <span class="font-medium text-gray-500">Disabled</span>
                    @endif
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Add an extra layer of security to your account
                </p>
            </div>

            <div>
                @if ($twoFactorEnabled)
                    <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Disable
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                            Enable
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Email Verification --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Email Verification</h2>
        
        <div class="flex items-center justify-between">
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
            </div>

            @if (!$user->hasVerifiedEmail())
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                        Resend Verification Email
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
