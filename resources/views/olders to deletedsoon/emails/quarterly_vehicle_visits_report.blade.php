<!-- Start Generation Here -->
<!DOCTYPE html>
<html lang="en-GB">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quarterly Vehicle Visits Report</title>
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3 style="color: #0c5460; margin: 0px !important; text-align: center; font-weight: bold; text-transform: uppercase;">
                Quarterly Vehicle Visits Report
            </h3>
        </div>

        <div class="section">
            <p>Hi Thiago,</p>
            <p>Please note that you are the only recipient of this email.</p>
            <p>Below is the quarterly report showing the most visited and least visited vehicle makes and models for the period {{ $emailData['periodStartFormatted'] }} to {{ $emailData['periodEndFormatted'] }}.</p>

            <!-- Most Visited Section -->
            <div class="section-title">Most Visited Vehicle Makes and Models (Showing only models with 80+ visits)</div>
            @if (count($emailData['mostVisited']) > 0)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Make (Total)</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Model</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: right; background-color: #f8f9fa; font-weight: bold;">Total Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentMake = null;
                            $rowCount = 0;
                        @endphp
                        @foreach ($emailData['mostVisited'] as $row)
                            @php
                                $rowCount++;
                                $makeDisplay = $row->make; // Already includes (total) from query
                                $makeOnly = $row->make_only ?? '';
                                
                                // Check if this is a new make group
                                $isNewMake = ($currentMake !== $makeOnly);
                                if ($isNewMake) {
                                    $currentMake = $makeOnly;
                                }
                            @endphp
                            <tr style="{{ $rowCount % 2 == 0 ? 'background-color: #f8f9fa;' : '' }}">
                                <td style="border: 1px solid #dee2e6; padding: 8px;{{ $isNewMake ? 'border-top: 2px solid #0c5460;' : '' }}">
                                    @if ($isNewMake)
                                        <strong style="color: #0c5460;">{{ $makeDisplay }}</strong>
                                    @else
                                        <span style="color: #6c757d;">&nbsp;</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ trim($row->model) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ number_format($row->total_visits) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #dc3545;">No data available for most visited vehicles (80+ visits).</p>
            @endif

            <!-- Least Visited Section -->
            <div class="section-title">Least Visited Vehicle Makes and Models (Showing only models with 5-80 visits)</div>
            @if (count($emailData['leastVisited']) > 0)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Make (Total)</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Model</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: right; background-color: #f8f9fa; font-weight: bold;">Total Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentMake = null;
                            $rowCount = 0;
                        @endphp
                        @foreach ($emailData['leastVisited'] as $row)
                            @php
                                $rowCount++;
                                $makeDisplay = $row->make; // Already includes (total) from query
                                $makeOnly = $row->make_only ?? '';
                                
                                // Check if this is a new make group
                                $isNewMake = ($currentMake !== $makeOnly);
                                if ($isNewMake) {
                                    $currentMake = $makeOnly;
                                }
                            @endphp
                            <tr style="{{ $rowCount % 2 == 0 ? 'background-color: #f8f9fa;' : '' }}">
                                <td style="border: 1px solid #dee2e6; padding: 8px;{{ $isNewMake ? 'border-top: 2px solid #0c5460;' : '' }}">
                                    @if ($isNewMake)
                                        <strong style="color: #0c5460;">{{ $makeDisplay }}</strong>
                                    @else
                                        <span style="color: #6c757d;">&nbsp;</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ trim($row->model) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ number_format($row->total_visits) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #dc3545;">No data available for least visited vehicles (5-80 visits).</p>
            @endif

            <!-- Most Repeated Model+Year Section -->
            <div class="section-title">Most Repeated Model + Year Combinations (Showing combinations with 10+ repeats)</div>
            @if (count($emailData['mostRepeatedModelYear'] ?? []) > 0)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Make</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Model</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Year</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: right; background-color: #f8f9fa; font-weight: bold;">Year (Repeat Count)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentMake = null;
                            $rowCount = 0;
                        @endphp
                        @foreach ($emailData['mostRepeatedModelYear'] as $row)
                            @php
                                $rowCount++;
                                $isNewMake = ($currentMake !== $row->make);
                                if ($isNewMake) {
                                    $currentMake = $row->make;
                                }
                            @endphp
                            <tr style="{{ $rowCount % 2 == 0 ? 'background-color: #f8f9fa;' : '' }}">
                                <td style="border: 1px solid #dee2e6; padding: 8px;{{ $isNewMake ? 'border-top: 2px solid #0c5460;' : '' }}">
                                    @if ($isNewMake)
                                        <strong style="color: #0c5460;">{{ $row->make }}</strong>
                                    @else
                                        <span style="color: #6c757d;">&nbsp;</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ trim($row->model) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->year }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ number_format($row->repeat_count) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #dc3545;">No data available for most repeated model+year combinations (10+ repeats).</p>
            @endif

            <!-- Least Repeated Model+Year Section -->
            <div class="section-title">Least Repeated Model + Year Combinations (Showing combinations with 5 or fewer repeats)</div>
            @if (count($emailData['leastRepeatedModelYear'] ?? []) > 0)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Make</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Model</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left; background-color: #f8f9fa; font-weight: bold;">Year</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: right; background-color: #f8f9fa; font-weight: bold;">Repeat Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentMake = null;
                            $rowCount = 0;
                        @endphp
                        @foreach ($emailData['leastRepeatedModelYear'] as $row)
                            @php
                                $rowCount++;
                                $isNewMake = ($currentMake !== $row->make);
                                if ($isNewMake) {
                                    $currentMake = $row->make;
                                }
                            @endphp
                            <tr style="{{ $rowCount % 2 == 0 ? 'background-color: #f8f9fa;' : '' }}">
                                <td style="border: 1px solid #dee2e6; padding: 8px;{{ $isNewMake ? 'border-top: 2px solid #0c5460;' : '' }}">
                                    @if ($isNewMake)
                                        <strong style="color: #0c5460;">{{ $row->make }}</strong>
                                    @else
                                        <span style="color: #6c757d;">&nbsp;</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ trim($row->model) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->year }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ number_format($row->repeat_count) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #dc3545;">No data available for least repeated model+year combinations (5 or fewer repeats).</p>
            @endif

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
                Notification is being generated in <code style="color:#bd4147;">emails/quarterly_vehicle_visits_report.blade.php</code> and this
                notification is sent from <code style="color:#bd4147;">Commands/SendQuarterlyVehicleVisitsReport.php</code>
                and emailer is in <code style="color:#bd4147;">app/Mail/QuarterlyVehicleVisitsReportMail.php</code></p>
        </div>
    </div>
</body>

</html>
<!-- End Generation Here -->

