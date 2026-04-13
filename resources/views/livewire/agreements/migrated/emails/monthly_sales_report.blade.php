{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Monthly motorbike sales report
</p>
<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin:0 0 16px;font-size:14px;color:#111827;line-height:1.5;">
    <thead>
        <tr style="background-color:#f4f4f4;">
            <th style="border:2px solid #000000;padding:8px;text-align:left;">Date</th>
            <th style="border:2px solid #000000;padding:8px;text-align:left;">Reg No</th>
            <th style="border:2px solid #000000;padding:8px;text-align:left;">Motorbike ID</th>
            <th style="border:2px solid #000000;padding:8px;text-align:left;">Status</th>
            <th style="border:2px solid #000000;padding:8px;text-align:left;">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                <td style="border:2px solid #000000;padding:8px;">{{ $row['date'] }}</td>
                <td style="border:2px solid #000000;padding:8px;">{{ $row['reg_no'] }}</td>
                <td style="border:2px solid #000000;padding:8px;">{{ $row['motorbike_id'] }}</td>
                <td style="border:2px solid #000000;padding:8px;">{{ $row['status'] }}</td>
                <td style="border:2px solid #000000;padding:8px;">{{ $row['user'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
