{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Order confirmation
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear {{ $order->full_name ?? 'customer' }},
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you for your order. Here are the details:
</p>
<ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#111827;line-height:1.65;">
    <li style="margin:4px 0;"><strong>Pickup address:</strong> {{ $order->pickup_address ?? '' }}</li>
    <li style="margin:4px 0;"><strong>Dropoff address:</strong> {{ $order->dropoff_address ?? '' }}</li>
    <li style="margin:4px 0;"><strong>Vehicle registration:</strong> {{ $order->vrm ?? '' }}</li>
    <li style="margin:4px 0;"><strong>Pickup date:</strong>
        @if(! empty($order->pick_up_datetime))
            {{ \Carbon\Carbon::parse($order->pick_up_datetime)->format('l, j F Y \a\t g:i A') }}
        @else
            N/A
        @endif
    </li>
    <li style="margin:4px 0;"><strong>Distance:</strong> {{ $order->distance ?? '' }} miles</li>
    <li style="margin:4px 0;"><strong>Note:</strong> {{ $order->note ?? '' }}</li>
</ul>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    We will contact you shortly with further details.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Best regards,<br>NGN Motors
</p>
