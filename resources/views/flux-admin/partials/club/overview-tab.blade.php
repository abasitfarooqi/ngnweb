<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Member Details</h2>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-zinc-200 dark:bg-zinc-700">
            @php
                $fields = [
                    'Full Name' => $member->full_name,
                    'Email' => $member->email,
                    'Phone' => $member->phone,
                    'Customer' => null,
                    'Vehicle' => trim($member->make . ' ' . $member->model . ' ' . $member->year) ?: null,
                    'VRM' => $member->vrm,
                    'Partner' => $member->partner?->companyname,
                    'Active?' => $member->is_active ? 'Yes' : 'No',
                    'T&C Agreed?' => $member->tc_agreed ? 'Yes' : 'No',
                    'Passkey' => $member->passkey,
                    'Email Sent?' => $member->email_sent ? 'Yes' : 'No',
                    'User' => $member->user?->first_name . ' ' . $member->user?->last_name,
                ];
            @endphp

            @foreach($fields as $label => $value)
                <div class="bg-white dark:bg-zinc-900 px-5 py-4">
                    <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ $label }}</dt>
                    <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">
                        @if($label === 'Customer' && $member->customer)
                            <a href="{{ route('flux-admin.customers.show', $member->customer_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $member->customer->full_name ?? $member->customer->first_name . ' ' . $member->customer->last_name }}
                            </a>
                        @elseif($label === 'Customer')
                            —
                        @else
                            {{ $value ?: '—' }}
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>
