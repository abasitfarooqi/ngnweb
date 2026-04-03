<!-- Start Generation Here -->
<!DOCTYPE html>
<html lang="en-GB">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annual Busiest Days & Months Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Helvetica:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Monospace&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-family-heading: 'Helvetica', sans-serif;
            --font-family-body: 'Monospace', monospace;
            --font-family-text: 'Roboto', sans-serif;
        }

        body {
            font-family: var(--font-family-text);
            margin: 0;
            padding: 0;
            background-color: #e7e7e7;
        }

        .container {
            max-width: 90%;
            margin: 10px auto;
            padding: 15px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .header {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .section {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .muted {
            color: #6c757d;
            font-size: 12px;
        }

        .section-title {
            color: #0c5460;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .branch-section {
            margin-bottom: 20px;
            padding: 10px 0;
        }

        .report-title {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 8px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3 style="color: #0c5460; margin: 0px !important; text-align: center; font-weight: bold; text-transform: uppercase;">
                Annual Busiest Days & Months Report
            </h3>
            <p style="color: #0c5460; margin: 5px 0 0 0; text-align: center; font-size: 14px;">
                Period: {{ \Carbon\Carbon::parse($emailData['yearStart'])->format('d M Y') }} to
                {{ \Carbon\Carbon::parse($emailData['yearEnd'])->format('d M Y') }}
            </p>
        </div>

        <div class="section">

        <p>Hi Thiago,</p>

        <p>Please note that you are the only recipient of this email.</p>

        <p>
            Below is the annual busiest days and months report for the period {{ \Carbon\Carbon::parse($emailData['yearStart'])->format('d M Y') }} to
            {{ \Carbon\Carbon::parse($emailData['yearEnd'])->format('d M Y') }}.
        </p>

            <!-- Section 1: Busiest Days (Aggregated) -->
            <div class="section-title">Busiest Days</div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Day Name</th>
                        <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Total Visits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($emailData['allDaysReport'] as $day)
                        <tr>
                            <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $day->day_name }}</td>
                            <td style="border: 1px solid #dee2e6; padding: 8px;"><strong>{{ $day->total_visits }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Section 2: Busiest Months (Aggregated) -->
            <div class="section-title">Busiest Months</div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Month Name</th>
                        <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Total Visits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($emailData['allMonthsReport'] as $month)
                        <tr>
                            <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $month->month_name }}</td>
                            <td style="border: 1px solid #dee2e6; padding: 8px;"><strong>{{ $month->total_visits }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Section 3-5: Individual Branch Reports -->
            @php
                $branchOrder = ['CATFORD', 'TOOTING', 'SUTTON'];
                $allBranchIds = array_unique(
                    array_merge(
                        array_keys($emailData['dayReportByBranch'] ?? []),
                        array_keys($emailData['monthReportByBranch'] ?? [])
                    )
                );
                // Sort branches to match desired order
                usort($allBranchIds, function($a, $b) use ($branchOrder) {
                    $aIndex = array_search($a, $branchOrder);
                    $bIndex = array_search($b, $branchOrder);
                    $aIndex = $aIndex !== false ? $aIndex : 999;
                    $bIndex = $bIndex !== false ? $bIndex : 999;
                    return $aIndex <=> $bIndex;
                });
            @endphp

            @foreach ($allBranchIds as $branchId)
                @php
                    $dayData = $emailData['dayReportByBranch'][$branchId] ?? null;
                    $monthData = $emailData['monthReportByBranch'][$branchId] ?? null;
                    $branchName = $dayData['branch_name'] ?? $monthData['branch_name'] ?? $branchId;
                @endphp

                <div class="section-title"> {{ $branchName }}</div>
                
                <div class="branch-section">
                    @if ($dayData && count($dayData['data']) > 0)
                        <div class="report-title">Busiest Day</div>
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Day Name</th>
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Total Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dayData['data'] as $row)
                                    <tr>
                                        <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->day_name }}</td>
                                        <td style="border: 1px solid #dee2e6; padding: 8px;"><strong>{{ $row->total_visits }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if ($monthData && count($monthData['data']) > 0)
                        <div class="report-title">Busiest Month</div>
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Month Name</th>
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Total Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthData['data'] as $row)
                                    <tr>
                                        <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->month_name }}</td>
                                        <td style="border: 1px solid #dee2e6; padding: 8px;"><strong>{{ $row->total_visits }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach

            <hr>
            <p style="margin: 0px !important;text-align: center;">This is an automated report by IT Department</p>
            <p style="margin: 0px !important;text-align: center;">(System health check and performance monitoring).</p>
            <small style="margin: 0px !important;text-align: center;">I.T Support email is added as Bcc for services
                health check.</small>
        </div>

        <div class="footer text-center" style="text-align: center;font-weight: bold !important;">
            <p style="margin: 0px !important;">Regards,</p>
            <p style="margin: 0px !important;">NGN IT Team</p>
            <p
                style="margin: 0px !important;margin-top: 10px !important;text-align: center;font-size: 12px;color: black;">
                Notification is being generated in <code style="color:#bd4147;">emails/annual_busiest_days_report.blade.php</code> and this
                notification is sent from <code style="color:#bd4147;">Commands/SendAnnualBusiestDaysReport.php</code>
                and emailer is in <code style="color:#bd4147;">app/Mail/AnnualBusiestDaysReportMail.php</code></p>
        </div>
    </div>
</body>

</html>
<!-- End Generation Here -->
