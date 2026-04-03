<style>
    /* Shared agreement signing: centred column, header row, signature modal (aligned with signature-contract-v6-merged; no rounded corners). */
    /*
     * These pages use @@vite (Tailwind) without Bootstrap. Legacy markup still uses Bootstrap 5
     * display utilities (d-md-none / d-none d-md-block) for duplicate mobile vs desktop tables.
     * Replicate the md breakpoint (768px) so only one variant shows at a time.
     */
    @@media (max-width: 767.98px) {
        .agreement-signing-page .d-md-none {
            display: block !important;
        }

        .agreement-signing-page .d-none.d-md-block,
        .agreement-signing-page .table-responsive.d-none.d-md-block {
            display: none !important;
        }
    }

    @@media (min-width: 768px) {
        .agreement-signing-page .d-md-none {
            display: none !important;
        }

        .agreement-signing-page .d-none.d-md-block,
        .agreement-signing-page .table-responsive.d-none.d-md-block {
            display: block !important;
        }
    }

    @@media print {
        .agreement-signing-page .d-md-none {
            display: none !important;
        }

        .agreement-signing-page .d-none.d-md-block,
        .agreement-signing-page .table-responsive.d-none.d-md-block {
            display: block !important;
        }
    }

    .agreement-signing-page .container {
        max-width: 58rem;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        box-sizing: border-box;
    }

    .agreement-brand-header-wrap {
        max-width: 58rem;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        margin-bottom: 10px;
        box-sizing: border-box;
    }

    .agreement-brand-header {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 1px solid #000;
        padding: 8px 12px;
        background-color: #f3f3f3;
        box-sizing: border-box;
    }

    .agreement-brand-header__logo {
        flex: 0 0 20%;
        max-width: 160px;
        min-width: 0;
    }

    .agreement-brand-header__logo-img {
        display: block;
        margin: 8px 0;
        max-width: 100%;
        height: auto;
    }

    .agreement-brand-header__address {
        flex: 1 1 auto;
        min-width: 0;
        font-size: 12px;
        line-height: 1.35;
        text-align: center;
    }

    .agreement-brand-header__title {
        flex: 0 0 28%;
        max-width: 16rem;
        min-width: 0;
        font-size: 15px;
        font-weight: bold;
        text-align: right;
        line-height: 1.2;
    }

    @@media (max-width: 767.98px) {
        .agreement-brand-header {
            flex-wrap: wrap;
            justify-content: center;
        }

        .agreement-brand-header__logo,
        .agreement-brand-header__address,
        .agreement-brand-header__title {
            flex: 1 1 100%;
            max-width: 100%;
            text-align: center;
        }

        .agreement-brand-header__title {
            text-align: center;
        }
    }

    .signature-area {
        margin-top: 20px;
        padding: 15px;
        background: #ececec;
        border-radius: 0;
    }

    .agreement-signature-modal-root .modal-content {
        min-height: 100vh;
        background-color: #1f2937;
        border: 0;
        border-radius: 0;
    }

    .agreement-signature-modal-root #sigpad {
        width: 100%;
        max-width: min(96vw, 980px);
        margin-left: auto;
        margin-right: auto;
        padding: 16px 12px 24px;
        box-sizing: border-box;
    }

    .agreement-signature-modal-root canvas.full-size-canvas,
    .agreement-signature-modal-root #sigpad canvas {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 320px !important;
        height: auto !important;
        margin-left: auto;
        margin-right: auto;
        display: block;
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 0;
        box-sizing: border-box;
    }

    .agreement-signing-page #sigpad {
        max-width: min(96vw, 980px);
        margin-left: auto;
        margin-right: auto;
        box-sizing: border-box;
    }

    .agreement-signing-page #sigpad canvas {
        min-height: 280px !important;
        border-radius: 0;
    }

    /* Override legacy .full-size-canvas width: 10% inside signing flows */
    .agreement-signing-page canvas.full-size-canvas {
        width: 100% !important;
        max-width: 100% !important;
    }

    .agreement-signing-page #agreementCheckbox {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #333;
        border-radius: 0;
        cursor: pointer;
        display: inline-block;
        vertical-align: middle;
        position: relative;
        background: #fff;
    }

    .agreement-signing-page #agreementCheckbox:hover {
        border-color: #dc3545;
    }

    .agreement-signing-page #agreementCheckbox:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    label a {
        color: #000 !important;
        text-decoration: none;
    }
</style>
