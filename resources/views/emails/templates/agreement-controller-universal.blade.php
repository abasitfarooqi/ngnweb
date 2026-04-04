@php
    $mailData = is_array($mailData ?? null) ? $mailData : [];
    $rawTitle = trim((string) ($mailData['title'] ?? $mailData['subject'] ?? 'NGN Motors Update'));
    $rawBody = trim((string) ($mailData['body'] ?? ''));
    $rawUrl = trim((string) ($mailData['url'] ?? $mailData['actionUrl'] ?? ''));
    $bodyHtml = trim((string) ($mailData['body_html'] ?? ''));

    if ($bodyHtml === '' && $rawBody !== '' && preg_match('/<[a-z][\s\S]*>/i', $rawBody)) {
        $bodyHtml = $rawBody;
        $rawBody = '';
    }

    $greetingOverride = trim((string) ($mailData['greetingName'] ?? ''));
    $customerName = $greetingOverride;
    if ($customerName === '' && isset($mailData['customer']) && is_object($mailData['customer'])) {
        $customerName = trim((string) (($mailData['customer']->first_name ?? '').' '.($mailData['customer']->last_name ?? '')));
    }
    if ($customerName === '') {
        $customerName = 'there';
    }

    $introLines = [];
    if (isset($mailData['introLines']) && is_array($mailData['introLines'])) {
        $introLines = array_values(array_filter(array_map('trim', $mailData['introLines']), fn ($line) => $line !== ''));
    }
    if ($bodyHtml === '' && $rawBody !== '' && $introLines === []) {
        $introLines = preg_split('/\r\n|\r|\n/', $rawBody) ?: [];
        $introLines = array_values(array_filter(array_map('trim', $introLines), fn ($line) => $line !== ''));
    }

    $details = $mailData['details'] ?? [];
    $details = is_array($details) ? $details : [];
    $outroLines = $mailData['outroLines'] ?? [];
    $outroLines = is_array($outroLines) ? $outroLines : [];
    $actionLabel = trim((string) ($mailData['actionLabel'] ?? ''));
    if ($actionLabel === '' && $rawUrl !== '') {
        $actionLabel = 'Open link';
    }
@endphp

@include('emails.templates.universal', [
    'subject' => $rawTitle !== '' ? $rawTitle : 'NGN Motors Update',
    'heading' => $rawTitle !== '' ? $rawTitle : 'NGN Motors Update',
    'greetingName' => $customerName,
    'introLines' => $introLines,
    'bodyHtml' => $bodyHtml !== '' ? $bodyHtml : null,
    'details' => $details,
    'actionUrl' => $rawUrl !== '' ? $rawUrl : null,
    'actionLabel' => $rawUrl !== '' ? $actionLabel : null,
    'outroLines' => $outroLines,
])
