{{-- Dompdf: full-bleed tiled watermark, zero page gutter, no watermark padding. --}}
<style id="agreement-pdf-print-theme">
    @@page {
        margin: 0;
    }

    html {
        margin: 0 !important;
        padding: 0 !important;
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
        background-color: #f4f6f9 !important;
        color: #111827;
        background-repeat: repeat !important;
        background-position: 0 0 !important;
        background-size: auto auto !important;
        background-origin: border-box !important;
        background-clip: border-box !important;
    }

    .watermark {
        margin: 0 !important;
        padding: 0 !important;
        color: rgba(15, 23, 42, 0.14) !important;
    }

    .watermark-area {
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        background-repeat: repeat !important;
        background-position: 0 0 !important;
        background-size: auto auto !important;
        background-origin: border-box !important;
    }

    .table-con th {
        background-color: #0f172a !important;
        color: #ffffff !important;
        font-weight: bold;
    }

    .fee-table th {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
</style>
