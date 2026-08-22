<!DOCTYPE html>
<html lang="en-GB">
<head><meta charset="UTF-8"><title>You have been referred to NGN Motors rentals</title></head>
<body>
<p>Hello {{ $referral->submitted_name }},</p>
<p>{{ $referrer_name }} has referred you to NGN Motors motorcycle rentals.</p>
<p>Open this link to view available bikes and enquire:</p>
<p><a href="{{ $share_url }}">{{ $share_url }}</a></p>
<p>NGN Motors</p>
</body>
</html>
