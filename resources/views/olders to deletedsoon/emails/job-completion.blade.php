<!DOCTYPE html>
<html>

<head>
    <title>DVLA Check Job Completed</title>
    <style>
        .green-tick {
            color: green;
            font-size: 2em;
        }

        .content {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="content">
        <p class="green-tick">✔️</p>
        <p>The DVLA check job has been completed successfully.</p>
        <p>Total motorbikes processed: {{ $mailData['totalProcessed'] }}/{{ $mailData['total'] }}</p>
        <p>Successfully updated: {{ $mailData['successCount'] }}</p>
        <p>Failed to update: {{ $mailData['failureCount'] }}</p>
        <br>
        <p>NGN I.T Department</p>
    </div>
</body>

</html>
