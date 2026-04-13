{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Daily summary: invoices due today
</p>
@if($emailDataList->isEmpty())
    <p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">No invoices are due today.</p>
@else
    <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin:0 0 16px;font-size:14px;color:#111827;line-height:1.5;">
        <thead>
            <tr style="background-color:#f4f4f4;">
                <th style="border:2px solid #000000;padding:8px;text-align:left;">Booking No</th>
                <th style="border:2px solid #000000;padding:8px;text-align:left;">Customer</th>
                <th style="border:2px solid #000000;padding:8px;text-align:left;">VIN</th>
                <th style="border:2px solid #000000;padding:8px;text-align:left;">Reg No</th>
                <th style="border:2px solid #000000;padding:8px;text-align:left;">Weekly rent</th>
                <th style="border:2px solid #000000;padding:8px;text-align:left;">Invoice date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($emailDataList as $data)
                <tr>
                    <td style="border:2px solid #000000;padding:8px;">{{ $data['booking_no'] }}</td>
                    <td style="border:2px solid #000000;padding:8px;">{{ $data['customer_name'] }}</td>
                    <td style="border:2px solid #000000;padding:8px;">{{ $data['vin_number'] }}</td>
                    <td style="border:2px solid #000000;padding:8px;">{{ $data['registration_number'] }}</td>
                    <td style="border:2px solid #000000;padding:8px;">£{{ $data['weekly_rent'] }}</td>
                    <td style="border:2px solid #000000;padding:8px;">{{ \Carbon\Carbon::parse($data['invoice_date'])->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
