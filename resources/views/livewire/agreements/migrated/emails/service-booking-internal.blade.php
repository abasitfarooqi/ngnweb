{{--
  Fragment only: for staff notification inside the universal mail shell (no duplicate logo/footer).
--}}
<p style="margin:0 0 12px;font-size:14px;color:#fff;line-height:1.65;">
    <strong>New service enquiry</strong> — {{ $booking->service_type }}
</p>
<p style="margin:0 0 14px;font-size:13px;color:#fff;line-height:1.65;">Submitted details:</p>
<ul style="margin:0 0 16px;padding-left:20px;font-size:14px;color:#fff;line-height:1.65;">
    <li style="margin:4px 0;"><strong>Service type:</strong> {{ $booking->service_type }}</li>
    <li style="margin:4px 0;"><strong>Full name:</strong> {{ $booking->fullname }}</li>
    <li style="margin:4px 0;"><strong>Phone:</strong> {{ $booking->phone }}</li>
    <li style="margin:4px 0;"><strong>Email:</strong> {{ $booking->email ?: '—' }}</li>
    <li style="margin:4px 0;"><strong>Registration number:</strong> {{ $booking->reg_no }}</li>
    @if(! empty($booking->booking_date))
        <li style="margin:4px 0;"><strong>Preferred date:</strong> {{ $booking->booking_date }}</li>
    @endif
    @if(! empty($booking->booking_time))
        <li style="margin:4px 0;"><strong>Preferred time:</strong> {{ $booking->booking_time }}</li>
    @endif
    @if(! empty($booking->description))
        <li style="margin:4px 0;"><strong>Notes / description:</strong> {{ $booking->description }}</li>
    @endif
</ul>
<p style="margin:0 0 14px;font-size:14px;color:#fff;line-height:1.65;">
    <strong>Mark as dealt:</strong>
    <a href="{{ url('ngn-admin/service-booking/' . ($booking->id ?? 'N/A') . '/edit') }}" style="color:#ffb3b3;">Open in admin</a>
</p>
