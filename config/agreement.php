<?php

return [
    'pdf_engine' => env('AGREEMENT_PDF_ENGINE', 'dompdf'),

    'brand' => [
        'web_logo_light' => env('AGREEMENT_WEB_LOGO_LIGHT', 'img/ngn-motor-logo-fit-small.png'),
        'web_logo_dark' => env('AGREEMENT_WEB_LOGO_DARK', 'img/ngn-motor-logo-fit-small.png'),
        'pdf_logo_local' => env('AGREEMENT_PDF_LOGO_LOCAL', 'img/ngn-motor-logo-fit-small.png'),
        'pdf_logo_remote' => env('AGREEMENT_PDF_LOGO_REMOTE', 'https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png'),
        'pdf_watermark_local' => env('AGREEMENT_PDF_WATERMARK_LOCAL', ''),
        'pdf_watermark_remote' => env('AGREEMENT_PDF_WATERMARK_REMOTE', 'https://neguinhomotors.co.uk/img/watermark.png'),
    ],
];
