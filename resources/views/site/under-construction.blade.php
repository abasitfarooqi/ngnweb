<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    @php $seconds = (int) ($autoRedirectSeconds ?? config('launch.auto_redirect_seconds', 0)); @endphp
    @if($seconds > 0)
        <meta http-equiv="refresh" content="{{ $seconds }};url={{ $liveUrl }}">
    @endif
    <title>NGN Motors — site under construction</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, sans-serif;
            background: #111827;
            color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .panel {
            max-width: 32rem;
            width: 100%;
            background: #1f2937;
            border: 1px solid #374151;
            padding: 2rem;
            text-align: center;
        }
        h1 {
            margin: 0 0 1rem;
            font-size: 1.75rem;
            font-weight: 700;
        }
        p {
            margin: 0 0 1rem;
            line-height: 1.6;
            color: #d1d5db;
        }
        a.btn {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: #dc2626;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
        a.btn:hover { background: #b91c1c; }
        .note { font-size: 0.875rem; color: #9ca3af; margin-top: 1.25rem; }
    </style>
</head>
<body>
    <div class="panel" role="main">
        <h1>Website Under Maintenance</h1>
        <p>
            This website is undergoing maintenance. Please visit our temporary website at <strong>neguinhomotors.co.uk</strong>.
        </p>
        <p>
            <strong>ngnmotors.co.uk</strong> will be our main website address in the future. Meanwhile, please use <strong>neguinhomotors.co.uk</strong> for all services.
        </p>
   
        <a class="btn" href="{{ $liveUrl }}">Visit neguinhomotors.co.uk</a>
        <p class="note">Do not place orders or enter personal details on this site until we announce go-live.</p>
    </div>
</body>
</html>
