{{-- Dompdf: page inset via @page margin only — never body/wrapper padding (causes right/bottom bleed). --}}
<style id="agreement-pdf-print-theme">
    @@page {
        margin: 12mm 14mm;
    }

    html {
        margin: 0 !important;
        padding: 0 !important;
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
        width: auto !important;
        max-width: none !important;
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

    table {
        table-layout: fixed !important;
        max-width: 100% !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }

    .table-con th,
    .table-con td,
    .fee-table th,
    .fee-table td {
        padding: 5px 6px !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
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

    .header {
        margin: 0 0 8px 0 !important;
        padding: 0 !important;
    }

    .header table {
        width: 100% !important;
        border: 2px solid #111827 !important;
        border-collapse: collapse !important;
    }

    .header td {
        border: none !important;
        vertical-align: middle !important;
        padding: 8px 10px !important;
    }

    .header .title {
        font-size: 15px !important;
        font-weight: 800 !important;
        text-align: right !important;
        color: #111827 !important;
        line-height: 1.2 !important;
    }

    .header .address {
        font-size: 10px !important;
        font-weight: 600 !important;
        color: #111827 !important;
        line-height: 1.35 !important;
    }

    .box,
    .box * {
        border-radius: 0 !important;
    }

    .box {
        border: 1px solid #111827 !important;
    }
</style>
