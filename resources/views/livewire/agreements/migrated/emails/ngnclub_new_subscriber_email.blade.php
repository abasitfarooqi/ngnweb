<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to NGN Club! - Neguinho Motors</title>
    <style>
        @@font-face {
            font-family: 'Open Sans';
            src: url('https://fonts.googleapis.com/css?family=Open+Sans:400,700');
        }

        body {
            font-family: 'Open Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #262626;
        }


        .container {

            max-width: 600px;
            margin: 0 auto;
            padding: 10px 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #171717;
            color: #c83334;
            padding: 13px 0;
            border-radius: 5px 5px 0 0;
            display: flex;
            align-items: center;
        }

        .theme-title-color {
            color: #c31924;
        }

        .logo {
            flex: 0 0 auto;
            height: 50px;
            width: 130px;
        }

        p {
            font-weight: 500;
        }

        h1 {
            flex: 1;
            text-align: center;
            margin: 0px !important;
            padding: 0px !important;
        }

        .content {
            padding: 0 10px 0px 10px;
        }

        .footer {
            text-align: center;

            font-size: 12px;
            color: #777;
        }

        a {
            color: #c31924;
            text-decoration: none;
        }

        ul {
            /* list-style-type: none; */
            padding: 0;
            margin: 0;
            margin-left: 20px;
        }

        ul li {
            padding: 0;
            margin: 0;
            margin-bottom: 4px;

        }

        @@media (max-width: 534px) {
            .container {
                padding: 4px;
            }

            .content {
                padding: 0 10px;
                border: 1px solid black;
                border-radius: 0 0 5px 5px;
            }

            .header {
                padding: 10px 0 5px 0;
                text-align: center;
                display: inherit !important;

                align-items: inherit;
            }

            .logo {
                clear: both;
                flex: inherit;
                height: 40px;
                width: 110px;
                text-align: center;
            }

            h1 {
                clear: both;
                font-weight: 400;
                font-size: 22px;
                display: inherit;
                margin-top: 18px;

            }

            .footer {
                font-size: 10px;

            }

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn_transparent_logo96.png" alt="NGN Club" class="logo">
            <h1>Welcome to NGN Club!</h1>
        </div>
        <div class="content">
            <h2 class="theme-title-color">Hello <span>{{ $clubMember->full_name }}</span>,</h2>
            <p>Thank you for subscribing to NGN Club! We are excited to have you on board. Here are your account
                details:</p>
            <ul>
                <li><strong>Phone Number:</strong> {{ $clubMember->phone }}</li>
                <li><strong>Password:</strong> {{ $passcode }}</li>
            </ul>
            <p>You can use this password along with your phone number to access <br><a
                    href="https://neguinhomotors.co.uk/ngn-club/subscribe?phone={{ $clubMember->phone }}"><strong>NGN
                        Club
                        dashboard</strong></a>.</p>

            <h3 class="theme-title-color">Terms and Conditions</h3>
            <ul class="" style="font-size:12px;font-weight: light;">
                <li>NGN Club loyalty credits (£) are non-transferable.</li>
                <li>Each person is limited to one account.</li>
                <li>Loyalty credits earned will be assigned to your account after each qualifying purchase. Previous
                    purchases made before joining the NGN Club are not eligible for credit.</li>
                <li>Member is responsible for keeping its account details safe.</li>
                <li>Credits will expire after 6 months of being added into member’s account.</li>
                <li>Credits cannot be used towards PCNs, Instalments, Rentals.</li>
                <li>Loyalty credits earned will be available after 48 hours.</li>
                <li>Members will earn 10% credit on each £1 spent on repairs, maintenances, accessories and MOT to be
                    used at any NGN stores.</li>
                <li>Members will earn 2% credit on each £1 spent on all motorbike purchases to be used at any NGN
                    stores.</li>
                <li>Loyalty credits earned can only be used against your next purchase.</li>
                <li>Members will need a verification code to use their credits.</li>
                <li>NGN Club reserves the rights to change or alter the terms and conditions of the loyalty scheme.</li>
                <li>All personal data is processed in accordance with the Data Protection Act 2018 based on General Data
                    Protection Regulation (GDPR).</li>
                <li>NGN may contact you for special offers and schemes.</li>
            </ul>

            <p>If you have any questions or need assistance, feel free to contact our support team.</p>

            <p class="theme-title-color">Best regards, <strong><br>NGN Club Team</strong></p>

            <div class="footer">
                <p>&copy; {{ date('Y') }} Neguinho Motors - NGN Club. All rights reserved.</p>
            </div>
        </div>
    </div>
    <!-- Code injected by live-server -->
    <script>
        // <![CDATA[  <-- For SVG support
        if ('WebSocket' in window) {
            (function() {
                function refreshCSS() {
                    var sheets = [].slice.call(document.getElementsByTagName("link"));
                    var head = document.getElementsByTagName("head")[0];
                    for (var i = 0; i < sheets.length; ++i) {
                        var elem = sheets[i];
                        var parent = elem.parentElement || head;
                        parent.removeChild(elem);
                        var rel = elem.rel;
                        if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() ==
                            "stylesheet") {
                            var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
                            elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date()
                                .valueOf());
                        }
                        parent.appendChild(elem);
                    }
                }
                var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
                var address = protocol + window.location.host + window.location.pathname + '/ws';
                var socket = new WebSocket(address);
                socket.onmessage = function(msg) {
                    if (msg.data == 'reload') window.location.reload();
                    else if (msg.data == 'refreshcss') refreshCSS();
                };
                if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
                    console.log('Live reload enabled.');
                    sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
                }
            })();
        } else {
            console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
        }
        // ]]>
    </script>
</body>

</html>
