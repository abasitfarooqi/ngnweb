<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Free TAX and MOT notification">
    <meta name="keywords" content="TAX, MOT, notification, free">
    <meta name="author" content="Neguinho Motors Ltd, Motorcycle Repair and Services">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="7 days">
    <meta name="language" content="English">
    <title>Free TAX and MOT notification</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 500px;
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="checkbox"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group input[type="checkbox"] {
            width: auto;
        }

        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-group label,
        .form-group input {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        /* Responsive adjustments */
        @@media (max-width: 600px) {

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="checkbox"],
            .form-group input[type="submit"] {
                font-size: 16px;
            }

            .form-group label {
                font-size: 14px;
            }

            .container {
                padding: 10px;
            }
        }
    </style>
    @include('livewire.agreements.partials.signing-vite-assets')
</head>

<body class="agreement-signing-page">
    <br>
    @if (session('success'))
    <div style="color: green; font-size: 16px; text-align: center; padding: 10px;">
        {{ session('success') }}
        <a href="\">Back</a>
    </div>
    @endif

    <div class="container">

        <x-agreements.theme-logo class="w-full" />

        <h3 style="text-align: center; ">FREE ALERT NOTIFICATION</h3>
        <h2 style="text-align: center; vertical-align: middle;">TAX & MOT</h2>
        <form action="/mottax-notify-submit" method="POST" onsubmit="return validateRegNo()">
            @csrf
            <div class="form-group">
                <label for="first_name"
                    style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                    First Name
                </label>
                <input type="text" id="first_name" name="first_name"
                    style="letter-spacing: 2px; text-transform: uppercase; font-size: 20px; text-align: center;" required>
            </div>
            <div class="form-group">
                <label for="last_name"
                    style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                    Last Name
                </label>
                <input type="text" id="last_name" name="last_name"
                    style="letter-spacing: 2px; text-transform: uppercase; font-size: 20px; text-align: center;" required>
            </div>
            <div class="form-group">
                <label for="email"
                    style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                    Email
                </label>
                <input type="email" id="email" name="email"
                    style="letter-spacing: 2px; text-transform: uppercase; font-size: 20px; text-align: center;" required>
            </div>
            <div class="form-group">
                <label for="reg_no"
                    style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                    Number Plate
                </label>
                <input type="text" id="reg_no" name="reg_no" required
                    style="letter-spacing: 2px; text-transform: uppercase; font-weight: bold;background-color: yellow; color: #000; font-size: 24px; text-align: center;">
                <div id="reg_no_error" class="error-message">Please enter a valid registration number.</div>
            </div>
            <div class="form-group">
                <label for="phone"
                    style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                    Phone Number
                </label>
                <input type="text" id="phone" name="phone"
                    style="letter-spacing: 2px; text-transform: uppercase;  font-size: 20px; text-align: center;" required>
            </div>
            <br>
            <div class="form-group">
                <input type="checkbox" id="notify_email" name="notify_email" checked>
                <label style="margin: 1px; font-size: 13px; padding:2px;" for="notify_email">Notify by Email</label>
            </div>
            <div class="form-group">
                <input type="checkbox" id="notify_phone" name="notify_phone">
                <label style="margin: 1px; font-size: 13px; padding:2px;" for="notify_phone">Notify by SMS</label>
            </div>
            <div class="form-group">
                <input type="checkbox" id="enable" name="enable" checked>
                <label style="margin: 1px; font-size: 13px; padding:2px;" for="enable">Opt in for Exclusive Deals and
                    Discounts on Accessories and Repairs</label>
            </div>
            <br>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
        <br>
        <div class="form-group">
            <label for="unsubscribe_note" style="letter-spacing: 0.5px; font-size: 10px; font-style: italic;color: gray">
                You can opt out at any time by emailing your number plate and the word "unsubscribe" to <a
                    style="color: rgb(115, 111, 111); vlink: none; text-decoration: none;"
                    href="mailto:customerservice@neguinhomotors.co.uk">customerservice@neguinhomotors.co.uk</a>.
            </label>
        </div>

        <div class="form-group" style="background-color: pink; padding: 10px;">
            <label style="color: black; font-weight: bold;">FREE ALERT SYSTEM START OPERATING BY 15th of AUGUST 2024,
                BUT YOU STILL CAN SUBSCRIBE FOR FREE SERVICE.</label>
        </div>
    </div>

    <script>
 function validateRegNo() {
        const regNo = document.getElementById('reg_no').value;
        const regNoPattern = /^[A-Z]{2}[0-9]{2}[A-Z]{3}$/; // Adjust this pattern as necessary
        const errorMessage = document.getElementById('reg_no_error');

        if (!regNoPattern.test(regNo)) {
            errorMessage.style.display = 'block';
            return false; // Prevent form submission
        }

        errorMessage.style.display = 'none';
        return true; // Allow form submission if valid
    }
</body>

</html>
