<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Profile Details</h2>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
            {{-- First Name --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">First Name</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->first_name ?? '—' }}</dd>
            </div>

            {{-- Last Name --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Last Name</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->last_name ?? '—' }}</dd>
            </div>

            {{-- Date of Birth --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Date of Birth</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->dob?->format('d M Y') ?? '—' }}</dd>
            </div>

            {{-- Email --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Email</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->email ?? '—' }}</dd>
            </div>

            {{-- Phone --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Phone</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->phone ?? '—' }}</dd>
            </div>

            {{-- Address --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Address</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->address ?? '—' }}</dd>
            </div>

            {{-- Postcode --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Postcode</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->postcode ?? '—' }}</dd>
            </div>

            {{-- Nationality --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Nationality</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->nationality ?? '—' }}</dd>
            </div>

            {{-- Licence Number --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Licence Number</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->license_number ?? '—' }}</dd>
            </div>

            {{-- Licence Expiry --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Licence Expiry</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->license_expiry_date?->format('d M Y') ?? '—' }}</dd>
            </div>

            {{-- Verification Status --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Verification Status</dt>
                <dd class="mt-1">
                    @php
                        $vColour = match($customer->verification_status) {
                            'verified' => 'green',
                            'pending' => 'amber',
                            'rejected' => 'red',
                            default => 'zinc',
                        };
                    @endphp
                    <flux:badge :color="$vColour" size="sm">{{ ucfirst($customer->verification_status ?? 'unverified') }}</flux:badge>
                </dd>
            </div>

            {{-- Rating --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Rating</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->rating ?? '—' }}</dd>
            </div>

            {{-- Registered --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Registered</dt>
                <dd class="mt-1">
                    <flux:badge :color="$customer->is_register ? 'green' : 'zinc'" size="sm">{{ $customer->is_register ? 'Yes' : 'No' }}</flux:badge>
                </dd>
            </div>

            {{-- Club Member --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Club Member</dt>
                <dd class="mt-1">
                    <flux:badge :color="$customer->is_club ? 'green' : 'zinc'" size="sm">{{ $customer->is_club ? 'Yes' : 'No' }}</flux:badge>
                </dd>
            </div>

            {{-- Preferred Branch --}}
            <div>
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Preferred Branch</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $customer->preferredBranch?->name ?? '—' }}</dd>
            </div>

            {{-- Reputation Note (full width) --}}
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Reputation Note</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white whitespace-pre-line">{{ $customer->reputation_note ?? '—' }}</dd>
            </div>
        </div>
    </div>
</div>
