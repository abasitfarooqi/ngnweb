<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cron Job Report</title>
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
            max-width: 600px;
            margin: 10px auto;
            padding: 15px;
            background-color: #c31924;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .header {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            colour: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: centre;
        }

        .section {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .footer {
            text-align: centre;
            margin-top: 20px;
            font-size: 12px;
            colour: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        
        <div class="header">
            <h1 style="color: #721c24;margin: 0px !important;"> Global Stock - Cron Job Status</h1>
            <p>The following cron jobs have been executed:</p>
        </div>
        <div class="section">
            <ul>
                <li>Total Products Processed: {{ $total_products }}</li>
                <li>Products with Positive Stock: {{ $positive_stock }}</li>
                <li>Products with Zero Stock: {{ $zero_stock }}</li>
                <li>Products with Negative Stock: {{ $negative_stock }}</li>
                <li>Total Stock Available: {{ number_format($total_stock, 2) }}</li>
            </ul>

            <hr>
            <p style="margin: 0px !important;text-align: center;">This is an automated report by IT Department</p>
            <p style="margin: 0px !important;text-align: center;">(System health check and performance monitoring).</p>

            
        </div>
        <div class="footer">
            <p style="margin: 0px !important;">Regards,</p>
            <p style="margin: 0px !important;">NGN IT Team</p>
            <p style="margin: 0px !important;margin-top: 10px !important;text-align: center;font-size: 12px;color: black;">Notification is being generated in <code style="color:#bd4147;">emails/cron-jobs/cron-job-global-stock-report.blade.php</code> and this notification is sent from <code style="color:#bd4147;">Commands/GlobalStockCommand.php</code> and emailer is in <code style="color:#bd4147;">app/Mail/CronJobReportMailer.php</code></p>
        </div>
    </div>
</body>

</html>