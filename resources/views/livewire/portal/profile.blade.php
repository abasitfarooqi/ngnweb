<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Profile</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Your profile is read-only. Contact NGN if you need any details updated.</p>

    <div class="max-w-2xl">
        <flux:card class="p-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Contact details</h2>
            <x-site.form-grid :cols="1">
                <flux:field>
                    <flux:label>Full name</flux:label>
                    <flux:input value="{{ $fullName ?: '—' }}" disabled class="bg-gray-100 dark:bg-gray-800" />
                </flux:field>
                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input value="{{ $email }}" disabled class="bg-gray-100 dark:bg-gray-800" />
                </flux:field>
                <flux:field>
                    <flux:label>Phone</flux:label>
                    <flux:input value="{{ $phone ?: '—' }}" disabled class="bg-gray-100 dark:bg-gray-800" />
                </flux:field>
                <flux:field>
                    <flux:label>WhatsApp</flux:label>
                    <flux:input value="{{ $whatsapp ?: '—' }}" disabled class="bg-gray-100 dark:bg-gray-800" />
                </flux:field>
            </x-site.form-grid>
        </flux:card>
    </div>
</div>
