<?php

return [
    'pdf_engine' => env('AGREEMENT_PDF_ENGINE', 'dompdf'),

    /** Minutes after signing before PDF is removed from public storage and uploaded to private DO Spaces. */
    'archive_delay_minutes' => (int) env('AGREEMENT_ARCHIVE_DELAY_MINUTES', 20),

    /** Prefix on DO Spaces (private objects — not linked in app). Covers rentals + finance contracts. */
    'spaces_archive_prefix' => env('AGREEMENT_SPACES_ARCHIVE_PREFIX', 'agreement-archives/'),

    'brand' => [
        'web_logo_light' => env('AGREEMENT_WEB_LOGO_LIGHT', 'img/ngn-motor-logo-fit-small-ngn.png'),
        'web_logo_dark' => env('AGREEMENT_WEB_LOGO_DARK', 'img/ngn-motor-logo-fit-small-ngn.png'),
        'pdf_logo_local' => env('AGREEMENT_PDF_LOGO_LOCAL', 'img/ngn-motor-logo-fit-small-ngn.png'),
        'pdf_logo_remote' => env('AGREEMENT_PDF_LOGO_REMOTE', 'https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small-ngn.png'),
        'pdf_watermark_local' => env('AGREEMENT_PDF_WATERMARK_LOCAL', 'img/watermark.png'),
        'pdf_watermark_remote' => env('AGREEMENT_PDF_WATERMARK_REMOTE', 'https://neguinhomotors.co.uk/img/watermark.png'),
    ],
];
