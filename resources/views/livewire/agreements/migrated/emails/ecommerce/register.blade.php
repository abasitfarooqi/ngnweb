{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Welcome to NGN Store
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>,
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you for joining us. We are pleased to have you with us. At NGN Store, we strive to provide you with the best shopping experience possible.
</p>
<p style="margin:0 0 8px;font-size:14px;color:#111827;line-height:1.65;">
    Feel free to explore our website and discover products tailored for you. Here is what we offer:
</p>
<ul style="margin:0 0 14px;padding-left:20px;font-size:14px;color:#111827;line-height:1.65;">
    <li style="margin:4px 0;">Motorbike and rider accessories</li>
    <li style="margin:4px 0;">Spare parts</li>
    <li style="margin:4px 0;">Professional repair services for Yamaha, Honda, Piaggio, Suzuki, Kawasaki, and more</li>
    <li style="margin:4px 0;">New and used motorbikes</li>
    <li style="margin:4px 0;">Motorcycle rentals</li>
    <li style="margin:4px 0;">Vehicle collection and delivery</li>
    <li style="margin:4px 0;">MOT services</li>
</ul>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Visit us online at <a href="https://ngnmotors.co.uk/shop" target="_blank" style="color:#c31924;">ngnmotors.co.uk/shop</a>
</p>
