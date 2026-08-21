@if($referredBy || $availablePoints > 0 || $pendingPoints > 0)
    <div class="mb-6 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Rental referral</h2>
        <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2 text-sm">
            @if($referredBy)
                <p>
                    Referred by
                    {{ $referredBy->referrer ? $referredBy->referrer->first_name.' '.$referredBy->referrer->last_name : 'customer #'.$referredBy->referrer_customer_id }}
                    ·
                    <a href="{{ route('flux-admin.rental-referrals.show', $referredBy) }}" class="text-blue-600 dark:text-blue-400 hover:underline">referral #{{ $referredBy->id }}</a>
                </p>
            @endif
            @if($availablePoints > 0)
                <p>Available reward: {{ $availablePoints }} points</p>
            @endif
            @if($pendingPoints > 0)
                <p>Pending points: {{ $pendingPoints }}</p>
            @endif
        </div>
    </div>
@endif
