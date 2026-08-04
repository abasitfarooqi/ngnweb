{{ $booking->subs_payment_date ? \Carbon\Carbon::createFromDate(2000, 1, (int) $booking->subs_payment_date)->format('jS') . ' of each month' : '-' }}
