<div>
    @if($referredBy || $availablePoints > 0 || $pendingPoints > 0)
        <div class="mb-6 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Rental referral</h2>
            <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                @if($referredBy)
                    <p>
                        Referred by
                        @if($referredBy->referrer)
                            <a href="{{ route('flux-admin.customers.show', $referredBy->referrer) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $referredBy->referrer->first_name }} {{ $referredBy->referrer->last_name }}
                            </a>
                        @else
                            customer #{{ $referredBy->referrer_customer_id }}
                        @endif
                        ·
                        <a href="{{ route('flux-admin.rental-referrals.show', $referredBy) }}" class="text-blue-600 dark:text-blue-400 hover:underline">referral #{{ $referredBy->id }}</a>
                    </p>
                    @if($referredBy->referred_qualifying_booking_id && (int) $referredBy->referred_qualifying_booking_id !== (int) $booking->id)
                        <p>
                            <a href="{{ route('flux-admin.rentals.show', $referredBy->referred_qualifying_booking_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Friend’s qualifying rental #{{ $referredBy->referred_qualifying_booking_id }}
                            </a>
                        </p>
                    @elseif($booking && (int) $booking->customer_id === (int) $referredBy->referred_customer_id)
                        <p>This rental is the referred customer’s hire.</p>
                    @endif
                    @if($referrerActiveBooking && (int) $referrerActiveBooking->id !== (int) $referredBy->referrer_qualifying_booking_id)
                        <p>
                            <a href="{{ route('flux-admin.rentals.show', $referrerActiveBooking) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Rental active when they referred #{{ $referrerActiveBooking->id }}
                            </a>
                        </p>
                    @endif
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
</div>
