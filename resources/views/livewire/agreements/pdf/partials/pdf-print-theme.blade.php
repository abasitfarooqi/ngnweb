{{-- Dompdf: equal page inset via @page margin; watermark uses cover (no stretch); .watermark text overlays. --}}
<style id="agreement-pdf-print-theme">
    @@page {
        margin: 14px;
    }

    html {
        margin: 0 !important;
        padding: 0 !important;
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
        background-color: #f4f6f9 !important;
        color: #111827;
        background-repeat: no-repeat !important;
        background-position: center center !important;
        background-size: cover !important;
        background-origin: border-box !important;
        background-clip: border-box !important;
    }

    .watermark {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        font-size: 12px !important;
        color: rgba(0, 0, 0, 0.1) !important;
        z-index: -1 !important;
        white-space: nowrap !important;
        pointer-events: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .watermark-area {
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
        min-height: 100% !important;
        border-radius: 0 !important;
        background-repeat: no-repeat !important;
        background-position: center center !important;
        background-size: cover !important;
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
