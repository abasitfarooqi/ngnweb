<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { font-size: 50px; }
        p { font-size: 20px; }
        .contact { margin-top: 30px; }
    </style>
</head>
<body>
    <h1><i class="fas fa-ban"></i> Access Denied</h1>
    <p>Your IP address ({{ request()->getClientIp() }}) is not recognised or access is denied outside working hours or system does not have understand as legit request.</p>
    <p>If you believe this is an error, please contact the administrator.</p>
    <div class="contact">
        <p><i class="fas fa-envelope pl-4" style="padding-right:6px"></i>customerservice@neguinhomotors.co.uk</p>
    </div>
</body>
</html>
