{{--
  Fragment only: consumed via UniversalMailPayload (body inner HTML) inside emails.templates.universal.
  Do not add outer html/head/body, logos, or branch footers — those come from the universal wrapper + x-emails.base.
--}}
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you for your enquiry regarding <strong>{{ $booking->service_type }}</strong>. Below is a summary of what you submitted.
</p>
<ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#111827;line-height:1.65;">
    <li style="margin:4px 0;"><strong>Service type:</strong> {{ $booking->service_type }}</li>
    <li style="margin:4px 0;"><strong>Full name:</strong> {{ $booking->fullname }}</li>
    <li style="margin:4px 0;"><strong>Phone:</strong> {{ $booking->phone }}</li>
    @if(! empty($booking->email))
        <li style="margin:4px 0;"><strong>Email:</strong> {{ $booking->email }}</li>
    @endif
    <li style="margin:4px 0;"><strong>Registration number:</strong> {{ $booking->reg_no }}</li>
    @if(! empty($booking->booking_date))
        <li style="margin:4px 0;"><strong>Preferred date:</strong> {{ $booking->booking_date }}</li>
    @endif
    @if(! empty($booking->booking_time))
        <li style="margin:4px 0;"><strong>Preferred time:</strong> {{ $booking->booking_time }}</li>
    @endif
    @if(! empty($booking->description))
        <li style="margin:4px 0;"><strong>Notes:</strong> {{ $booking->description }}</li>
    @endif
</ul>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    We will contact you shortly to confirm and schedule your service.
</p>
