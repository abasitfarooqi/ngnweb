@php
    $invoices = $invoices ?? [];
    $missing = (bool) ($missing ?? false);
    $message = $message ?? null;
    $bookingId = $bookingId ?? null;
@endphp
@if($missing)
    <p class="text-xs text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-950/30 p-2">
        {{ $message ?: \App\Models\RentingFreeWeekAward::ELIGIBILITY_FALLBACK }}
    </p>
@elseif($invoices !== [])
    <p class="text-xs text-zinc-500 mb-1">
        Last valid paid invoices
        @if($bookingId)
            on <a href="{{ route('flux-admin.rentals.show', $bookingId) }}" class="text-blue-600 dark:text-blue-400 hover:underline" wire:click.stop>booking #{{ $bookingId }}</a>
        @endif
    </p>
    <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-xs">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-500">
                <tr>
                    <th class="text-left font-semibold p-1.5">Invoice ID</th>
                    <th class="text-left font-semibold p-1.5">Tran. no</th>
                    <th class="text-left font-semibold p-1.5">Invoice date</th>
                    <th class="text-left font-semibold p-1.5">Invoice amount</th>
                    <th class="text-left font-semibold p-1.5">Paid amount</th>
                    <th class="text-left font-semibold p-1.5">Paid date</th>
                    <th class="text-left font-semibold p-1.5">Invoice state</th>
                    <th class="text-left font-semibold p-1.5">Deposit</th>
                    <th class="text-left font-semibold p-1.5">Received by</th>
                    <th class="text-left font-semibold p-1.5">Posting time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $row)
                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                        <td class="p-1.5">#{{ $row['invoice_id'] ?? $row['id'] ?? '—' }}</td>
                        <td class="p-1.5">{{ $row['transaction_no'] ?: '—' }}</td>
                        <td class="p-1.5">{{ ! empty($row['invoice_date']) ? \Carbon\Carbon::parse($row['invoice_date'])->format('d M Y') : '—' }}</td>
                        <td class="p-1.5">£{{ number_format((float) ($row['invoice_amount'] ?? 0), 2) }}</td>
                        <td class="p-1.5">£{{ number_format((float) ($row['paid_amount'] ?? 0), 2) }}</td>
                        <td class="p-1.5">{{ ! empty($row['paid_date']) ? \Carbon\Carbon::parse($row['paid_date'])->format('d M Y') : '—' }}</td>
                        <td class="p-1.5">{{ $row['invoice_state'] ?: '—' }}</td>
                        <td class="p-1.5">£{{ number_format((float) ($row['deposit'] ?? 0), 2) }}</td>
                        <td class="p-1.5">{{ $row['received_by'] ?: '—' }}</td>
                        <td class="p-1.5">{{ ! empty($row['posting_time']) ? \Carbon\Carbon::parse($row['posting_time'])->format('d M Y H:i') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
