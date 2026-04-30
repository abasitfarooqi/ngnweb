<aside class="border border-zinc-200 bg-zinc-50 p-5 text-sm dark:border-zinc-800 dark:bg-zinc-950">
    <h3 class="mb-3 font-semibold text-zinc-900 dark:text-white">Summary</h3>
    <dl class="space-y-2">
        <div class="flex justify-between"><dt class="text-zinc-500">Vehicle</dt><dd class="font-medium">{{ $selectedMotorbike?->reg_no ?: '—' }}</dd></div>
        <div class="flex justify-between"><dt class="text-zinc-500">Make / model</dt><dd class="text-right">{{ $selectedMotorbike?->make }} {{ $selectedMotorbike?->model }}</dd></div>
        <div class="flex justify-between"><dt class="text-zinc-500">Customer</dt><dd class="text-right">{{ $selectedCustomer?->first_name }} {{ $selectedCustomer?->last_name }}</dd></div>
        <div class="flex justify-between"><dt class="text-zinc-500">Email</dt><dd class="truncate max-w-[14rem] text-right">{{ $selectedCustomer?->email }}</dd></div>
        <div class="flex justify-between"><dt class="text-zinc-500">Weekly rent</dt><dd>£{{ number_format((float) ($weeklyRent ?? 0), 2) }}</dd></div>
        <div class="flex justify-between"><dt class="text-zinc-500">Deposit</dt><dd>£{{ number_format((float) $deposit, 2) }}</dd></div>
        <div class="flex justify-between"><dt class="text-zinc-500">Initial payment</dt><dd>£{{ number_format((float) $initialPayment, 2) }}</dd></div>
        <div class="flex justify-between"><dt class="text-zinc-500">Start</dt><dd>{{ $startDate }}</dd></div>
    </dl>
    <p class="mt-4 text-xs text-zinc-500">Booking is saved in <strong>DRAFT</strong>. Documents, signature and postings are handled from the booking detail page after creation.</p>
</aside>
