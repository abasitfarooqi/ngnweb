<!DOCTYPE html>
<html>

<head>

    <title>Daily Club Topup Report Job Completed</title>

</head>

<body>
    <h3>To: I.T Department</h3>
    <small>This is an automated email from the system. It is used to notify the I.T Department that the invoice
        generation has been completed.</small>
    <p>Below is the report of the invoices generated today.</p>
    <p>Invoices processed: {{ $data['totalProcessed'] }}</p>
    <p>Invoices created: {{ $data['newInvoices'] }}</p>
    <br>
    <p>Regards,</p>
    <p>NGN I.T Department</p>
</body>

</html>
