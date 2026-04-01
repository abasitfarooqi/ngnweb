@php
    $safeVerificationUrl = $verificationUrl ?? $url ?? '';
@endphp

@include('emails.templates.universal', [
    'subject' => 'Verify your email - NGN Motors',
    'heading' => 'Verify your email address',
    'greetingName' => optional($customer)->first_name ?? 'there',
    'introLines' => [
        'Thank you for creating your NGN Motors account.',
        'Please confirm your email address to activate your account and continue using all portal and store features.',
    ],
    'actionLabel' => 'Verify email address',
    'actionUrl' => $safeVerificationUrl,
    'details' => [
        'Account Email' => optional($customer)->email ?? '',
        'Verification Link Expiry' => '60 minutes',
    ],
    'outroLines' => [
        "If you did not create this account, you can safely ignore this email.",
    ],
])
