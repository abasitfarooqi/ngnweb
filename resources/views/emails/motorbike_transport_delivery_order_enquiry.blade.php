{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Motorcycle collection / delivery enquiry
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    DEAR {{ strtoupper($order->full_name ?? '') }},
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    THANK YOU FOR YOUR RECOVERY REQUEST. WE HAVE SUCCESSFULLY RECEIVED IT, AND HERE ARE THE DETAILS:
</p>
<ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#111827;line-height:1.65;">
    <li style="margin:4px 0;"><strong>ENQUIRY ID:</strong> {{ strtoupper((string) ($order->order_id ?? '')) }}</li>
    <li style="margin:4px 0;"><strong>CUSTOMER NAME:</strong> {{ strtoupper($order->full_name ?? '') }}</li>
    <li style="margin:4px 0;"><strong>CUSTOMER EMAIL:</strong> {{ strtoupper($order->email ?? '') }}</li>
    <li style="margin:4px 0;"><strong>CUSTOMER PHONE:</strong> {{ strtoupper($order->phone ?? '') }}</li>
    <li style="margin:4px 0;"><strong>CUSTOMER ADDRESS:</strong> {{ strtoupper($order->customer_address ?? '') }}</li>
    <li style="margin:4px 0;"><strong>CUSTOMER POSTCODE:</strong> {{ strtoupper($order->customer_postcode ?? '') }}</li>
    <li style="margin:8px 0;list-style:none;border-top:1px solid #4b5563;height:0;padding:0;line-height:0;">&nbsp;</li>
    <li style="margin:4px 0;"><strong>VEHICLE REGISTRATION:</strong> {{ strtoupper($order->vrm ?? '') }}</li>
    <li style="margin:4px 0;"><strong>VEHICLE TYPE:</strong> {{ strtoupper($order->vehicle_type ?? '') }}</li>
    <li style="margin:4px 0;"><strong>MOVEABLE:</strong> {{ $order->moveable ? 'YES' : 'NO' }}</li>
    <li style="margin:4px 0;"><strong>DOCUMENTS:</strong> {{ $order->documents ? 'YES' : 'NO' }}</li>
    <li style="margin:4px 0;"><strong>KEYS:</strong> {{ $order->keys ? 'YES' : 'NO' }}</li>
    <li style="margin:4px 0;"><strong>NOTE:</strong> {{ strtoupper($order->note ?? '') }}</li>
    <li style="margin:8px 0;list-style:none;border-top:1px solid #4b5563;height:0;padding:0;line-height:0;">&nbsp;</li>
    <li style="margin:4px 0;"><strong>PICKUP DATE:</strong> {{ \Carbon\Carbon::parse($order->pick_up_datetime)->format('l, j F Y \a\t g:i A') }}</li>
    <li style="margin:4px 0;"><strong>PICKUP ADDRESS:</strong> {{ strtoupper($order->pickup_address ?? '') }}</li>
    <li style="margin:4px 0;"><strong>DROPOFF ADDRESS:</strong> {{ strtoupper($order->dropoff_address ?? '') }}</li>
    <li style="margin:8px 0;list-style:none;border-top:1px solid #4b5563;height:0;padding:0;line-height:0;">&nbsp;</li>
    <li style="margin:4px 0;"><strong>APPROXIMATE DISTANCE:</strong> {{ strtoupper((string) ($order->distance ?? '')) }} MILES</li>
    <li style="margin:4px 0;"><strong>TOTAL COST:</strong> £{{ number_format($order->total_cost, 2) }}</li>
</ul>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    WE WILL CONTACT YOU SHORTLY TO CONFIRM YOUR REQUEST. WE UNDERSTAND THAT THIS CAN BE A STRESSFUL TIME, AND WE APPRECIATE YOUR TRUST IN US. WE WILL DO OUR BEST TO RESPOND TO YOUR REQUEST AS SOON AS POSSIBLE.
</p>
<p style="margin:0 0 16px;font-size:14px;color:#991b1b;line-height:1.65;">
    PLEASE NOTE THAT THIS IS AN ESTIMATE COST / DISTANCE AND THE FINAL COST MAY VARY BASED ON THE ACTUAL DISTANCE AND ANY ADDITIONAL FACTORS.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    BEST REGARDS,<br>NGN TEAM
</p>
