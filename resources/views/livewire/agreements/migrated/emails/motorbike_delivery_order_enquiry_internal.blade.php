{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Internal: motorcycle collection / delivery enquiry
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    HI NGN TEAM,
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    WE HAVE RECEIVED THE FOLLOWING ENQUIRY:
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
    <li style="margin:4px 0;"><strong>MOVEABLE:</strong> {{ strtoupper($order->moveable ? 'YES' : 'NO') }}</li>
    <li style="margin:4px 0;"><strong>DOCUMENTS:</strong> {{ strtoupper($order->documents ? 'YES' : 'NO') }}</li>
    <li style="margin:4px 0;"><strong>KEYS:</strong> {{ strtoupper($order->keys ? 'YES' : 'NO') }}</li>
    <li style="margin:4px 0;"><strong>NOTE:</strong> {{ strtoupper($order->note ?? '') }}</li>
    <li style="margin:8px 0;list-style:none;border-top:1px solid #4b5563;height:0;padding:0;line-height:0;">&nbsp;</li>
    <li style="margin:4px 0;"><strong>PICKUP DATE:</strong> {{ strtoupper(\Carbon\Carbon::parse($order->pick_up_datetime)->format('l, j F Y \a\t g:i A')) }}</li>
    <li style="margin:4px 0;"><strong>PICKUP ADDRESS:</strong> {{ strtoupper($order->pickup_address ?? '') }}</li>
    <li style="margin:4px 0;"><strong>DROPOFF ADDRESS:</strong> {{ strtoupper($order->dropoff_address ?? '') }}</li>
    <li style="margin:8px 0;list-style:none;border-top:1px solid #4b5563;height:0;padding:0;line-height:0;">&nbsp;</li>
    <li style="margin:4px 0;"><strong>APPROXIMATE DISTANCE:</strong> {{ strtoupper((string) ($order->distance ?? '')) }} MILES</li>
    <li style="margin:4px 0;"><strong>TOTAL COST:</strong> £{{ number_format($order->total_cost, 2) }}</li>
</ul>
<p style="margin:0 0 8px;font-size:14px;color:#111827;line-height:1.65;font-weight:700;">
    BEFORE MAKING A CALL, JUST DOUBLE CHECK THE FOLLOWING DETAILS:
</p>
<ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#111827;line-height:1.65;">
    <li style="margin:4px 0;">ENGINE SIZE (CC) (I.E., 125CC, 250CC, 600CC, ETC.)</li>
    <li style="margin:4px 0;">VALIDATE THE DISTANCE. YOU MIGHT USE NGN MANAGER TO VALIDATE THE COST.</li>
    <li style="margin:4px 0;">CHECK THE AVAILABILITY OF THE LOGISTICS TEAM.</li>
    <li style="margin:4px 0;">IF THE GIVEN DATE AND TIME IS TODAY, IT IS SUBJECT TO EXPRESS DELIVERY CHARGES. (£20)</li>
    <li style="margin:4px 0;">IF YOU DO NOT HAVE THE DRIVER OR AVAILABILITY, PLEASE LET THE CUSTOMER KNOW AND PROVIDE ALTERNATIVE OPTIONS.</li>
</ul>
<p style="margin:0 0 8px;font-size:14px;color:#111827;line-height:1.65;font-weight:700;">
    KEY QUESTIONS TO ASK THE CUSTOMER:
</p>
<ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#111827;line-height:1.65;">
    <li style="margin:4px 0;">IS THE BIKE MOVEABLE OR DOES IT REQUIRE A LIFT? (£15 ADDITIONAL CHARGE FOR LIFT)</li>
    <li style="margin:4px 0;">ARE YOU THE OWNER/KEEPER OF THE BIKE?</li>
    <li style="margin:4px 0;">DO YOU HAVE THE KEYS?</li>
    <li style="margin:4px 0;">SEND PAYMENT DETAILS TO THE CUSTOMER AND CONFIRM THE PAYMENT.</li>
</ul>
@if(! empty($order->id))
    <p style="margin:0 0 16px;font-size:14px;color:#111827;line-height:1.65;">
        Mark as dealt (if you have dealt with this query):
        <a href="{{ url('/ngn-admin/motorbike-delivery-order-enquiries/'.$order->id.'/edit') }}" style="color:#c31924;text-decoration:underline;">Mark as dealt</a>
    </p>
@endif
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    BEST REGARDS,<br>NGN TEAM
</p>
