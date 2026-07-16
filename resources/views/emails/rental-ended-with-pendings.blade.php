<x-mail::message>
# Rental ended with outstanding balances

Staff collected / ended a rental while balances remained unpaid.

**Booking:** #{{ $d['booking_id'] }}  
**Customer:** {{ $d['customer_name'] ?? '—' }}  
**Ended by:** {{ $d['staff_name'] ?? '—' }} (user #{{ $d['staff_id'] ?? '—' }})  
**Collect date / time:** {{ $d['collect_date'] ?? '—' }} {{ $d['collect_time'] ?? '' }}

## Outstanding (due on or before collect date)

| Type | Amount |
|:-----|-------:|
| Unpaid rent (invoices) | £{{ number_format((float) ($d['rental'] ?? 0), 2) }} |
| Other charges | £{{ number_format((float) ($d['additional'] ?? 0), 2) }} |
| Open PCN | £{{ number_format((float) ($d['pcn'] ?? 0), 2) }} |
| **Total** | **£{{ number_format((float) ($d['total'] ?? 0), 2) }}** |

Future invoices after the collect date were removed and are not included above.

@if(!empty($d['show_url']))
<x-mail::button :url="$d['show_url']">
Open rental in Flux Admin
</x-mail::button>
@endif

This staff member accepted responsibility for ending while money was still owed.
</x-mail::message>
