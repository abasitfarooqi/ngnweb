@php
    $__wmCssUrl = isset($agreementSigningWatermarkSrc) && $agreementSigningWatermarkSrc !== ''
        ? (string) $agreementSigningWatermarkSrc
        : ((string) config('agreement.brand.pdf_watermark_local', '') !== ''
            ? asset((string) config('agreement.brand.pdf_watermark_local'))
            : (string) config('agreement.brand.pdf_watermark_remote', ''));
@endphp
<style>
    /* Shared agreement signing: centred column, header row, signature modal (aligned with signature-contract-v6-merged; no rounded corners). */
    /*
     * Signing pages use agreement-signing-public.css (Bootstrap) without the main Tailwind bundle.
     * display utilities (d-md-none / d-none d-md-block) for duplicate mobile vs desktop tables.
     * Replicate the md breakpoint (768px) so only one variant shows at a time.
     */
    @@media (max-width: 767.98px) {
        .agreement-signing-page .d-md-none {
            display: block !important;
        }

        /* Desktop-only blocks: keep hidden even if Bootstrap utilities are overridden. */
        .agreement-signing-page .d-none.d-md-block,
        .agreement-signing-page .d-none.d-md-flex,
        .agreement-signing-page .d-none.d-md-table,
        .agreement-signing-page .d-none.d-md-inline,
        .agreement-signing-page .d-none.d-md-inline-block,
        .agreement-signing-page .table-responsive.d-none.d-md-block,
        .agreement-signing-page .table-responsive.d-none {
            display: none !important;
        }

        /* Nested desktop wrappers that somehow leak through */
        .agreement-signing-page .d-md-none .d-none.d-md-block {
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

        .agreement-signing-page .d-none.d-md-flex {
            display: flex !important;
        }

        .agreement-signing-page .d-none.d-md-table {
            display: table !important;
        }
    }

    @@media print {
        @@page {
            margin: 14px;
        }

        .agreement-signing-page .d-md-none {
            display: none !important;
        }

        .agreement-signing-page .d-none.d-md-block,
        .agreement-signing-page .table-responsive.d-none.d-md-block {
            display: block !important;
        }

        .agreement-signing-page .agreement-theme-toolbar {
            display: none !important;
        }
    }

    @if ($__wmCssUrl !== '')
        .agreement-signing-page {
            background-image: url("{{ e($__wmCssUrl) }}");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            background-origin: padding-box;
            background-clip: padding-box;
            padding: 16px;
            box-sizing: border-box;
        }
    @endif

    .agreement-signing-page {
        overflow-x: clip;
        -webkit-text-size-adjust: 100%;
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

    .agreement-theme-toolbar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 8px;
    }

    /* Beat agreement-signing-public.css icon-button rules (fixed 2.5rem square). */
    .agreement-signing-page .agreement-theme-toggle,
    .agreement-theme-toggle {
        appearance: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: auto !important;
        height: auto !important;
        min-height: 2rem;
        border: 1px solid #111827;
        background: #ffffff;
        color: #111827;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        padding: 8px 12px !important;
        cursor: pointer;
        border-radius: 0;
        white-space: nowrap;
    }

    .agreement-brand-header {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 2px solid #111827;
        padding: 10px 14px;
        color: #111827;
        background-color: #ffffff;
        box-sizing: border-box;
    }

    .agreement-brand-header__logo {
        flex: 0 0 20%;
        max-width: 160px;
        min-width: 0;
    }

    .agreement-brand-header__logo-img {
        display: block;
        margin: 0;
        max-width: 100%;
        height: auto;
    }

    /* Theme logos: only one visible. !important beats .agreement-brand-logo-wrap img { display:block }. */
    .agreement-signing-page .agreement-logo,
    .agreement-brand-logo-wrap .agreement-logo {
        max-width: 150px;
        width: 100%;
        height: auto;
        margin: 0;
    }

    .agreement-signing-page .agreement-logo--light,
    .agreement-brand-logo-wrap .agreement-logo--light,
    html[data-agreement-theme="light"] .agreement-logo--light,
    body.agreement-signing-page[data-theme="light"] .agreement-logo--light {
        display: block !important;
    }

    .agreement-signing-page .agreement-logo--dark,
    .agreement-brand-logo-wrap .agreement-logo--dark,
    html[data-agreement-theme="light"] .agreement-logo--dark,
    body.agreement-signing-page[data-theme="light"] .agreement-logo--dark {
        display: none !important;
    }

    /* Fee / contract tables: dark head needs light text in light mode. */
    .agreement-signing-page .fee-table th,
    .agreement-signing-page table.fee-table thead th,
    .agreement-signing-page table.fee-table > thead > tr > th,
    .agreement-signing-page table.fee-table > tbody > tr > th {
        background-color: #111827 !important;
        color: #ffffff !important;
        font-weight: 700;
    }

    .agreement-signing-page .fee-table td {
        color: #111827;
        background-color: #ffffff;
    }

    /* Customer / section bars that use th/td headers on light pages */
    .agreement-signing-page .table-con th,
    .agreement-signing-page .card-header {
        color: #111827 !important;
    }

    .agreement-brand-header__address {
        flex: 1 1 auto;
        min-width: 0;
        font-size: 12px;
        line-height: 1.35;
        text-align: center;
        color: #111827;
        font-weight: 600;
    }

    .agreement-brand-header__title {
        flex: 0 0 28%;
        max-width: 16rem;
        min-width: 0;
        font-size: 15px;
        font-weight: 800;
        text-align: right;
        line-height: 1.2;
        color: #111827;
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
        appearance: auto !important;
        -webkit-appearance: checkbox !important;
        width: 18px !important;
        height: 18px !important;
        margin-right: 8px;
        cursor: pointer;
        vertical-align: middle;
        accent-color: #dc3545;
        opacity: 1 !important;
        pointer-events: auto !important;
        position: static !important;
        background: initial !important;
        border: initial !important;
    }

    label a {
        color: #b91c1c !important;
        text-decoration: underline;
        font-weight: 700;
    }

    /* ——— Mobile contract layout ——— */
    @@media (max-width: 767.98px) {
        .agreement-signing-page {
            padding: 8px !important;
            font-size: 13px;
            line-height: 1.45;
        }

        .agreement-signing-page .container,
        .agreement-signing-page .container-fluid,
        .agreement-brand-header-wrap {
            max-width: 100% !important;
            width: 100% !important;
            padding-left: 8px !important;
            padding-right: 8px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .agreement-theme-toolbar {
            position: sticky;
            top: 0;
            z-index: 30;
            background: inherit;
            padding: 6px 0;
            margin-bottom: 6px;
        }

        .agreement-signing-page .agreement-theme-toggle,
        .agreement-theme-toggle {
            min-height: 2.5rem;
            padding: 10px 14px !important;
            font-size: 13px;
        }

        .agreement-brand-header {
            flex-direction: column;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 10px;
        }

        .agreement-brand-header__logo,
        .agreement-brand-header__address,
        .agreement-brand-header__title {
            flex: 1 1 100% !important;
            max-width: 100% !important;
            width: 100% !important;
            text-align: center !important;
        }

        .agreement-brand-header__logo {
            max-width: 140px !important;
        }

        .agreement-brand-header__logo .agreement-logo {
            margin-left: auto;
            margin-right: auto;
        }

        .agreement-brand-header__address {
            font-size: 11px !important;
            word-break: break-word;
        }

        .agreement-brand-header__title {
            font-size: 14px !important;
            line-height: 1.25 !important;
            max-width: 100% !important;
        }

        .agreement-signing-page .agreement-expiry-banner {
            font-size: 11px !important;
            padding: 8px !important;
            margin: 0 0 10px !important;
            line-height: 1.35;
        }

        /* Mobile cards (customer / licence / vehicle blocks) */
        .agreement-signing-page .d-md-none .card {
            border: 1px solid #111827 !important;
            border-radius: 0 !important;
            margin-bottom: 12px;
            overflow: hidden;
            background: #fff;
        }

        .agreement-signing-page .d-md-none .card-header {
            background: #f3f4f6 !important;
            color: #111827 !important;
            font-weight: 800;
            font-size: 13px;
            text-align: center;
            padding: 10px 12px;
            border-bottom: 1px solid #111827 !important;
            border-radius: 0 !important;
        }

        .agreement-signing-page .d-md-none .list-group-item {
            padding: 10px 12px;
            font-size: 13px;
            word-break: break-word;
            border-radius: 0 !important;
            border-color: #d1d5db !important;
        }

        .agreement-signing-page .d-md-none > br {
            display: none;
        }

        .agreement-signing-page .d-md-none + .d-md-none {
            margin-top: 8px;
        }

        /* Tables that remain on mobile (fee schedules, terms tables) */
        .agreement-signing-page .fee-table,
        .agreement-signing-page .table-con,
        .agreement-signing-page .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-left: 0;
            margin-right: 0;
        }

        .agreement-signing-page .fee-table {
            font-size: 12px;
            margin-top: 12px;
            margin-bottom: 12px;
            border: 1px solid #333;
        }

        .agreement-signing-page .fee-table th,
        .agreement-signing-page .fee-table td {
            padding: 8px 10px !important;
            min-width: 7rem;
            vertical-align: top;
            white-space: normal;
            word-break: break-word;
        }

        .agreement-signing-page .fee-table th:first-child,
        .agreement-signing-page .fee-table td:first-child {
            position: sticky;
            left: 0;
            z-index: 1;
            min-width: 8.5rem;
            box-shadow: 2px 0 0 rgba(0, 0, 0, 0.08);
        }

        .agreement-signing-page .fee-table th:first-child {
            background-color: #111827 !important;
            color: #ffffff !important;
        }

        .agreement-signing-page .fee-table td:first-child {
            background-color: #ffffff;
            font-weight: 600;
        }

        .agreement-signing-page p,
        .agreement-signing-page li {
            font-size: 13px;
            word-break: break-word;
        }

        .agreement-signing-page .attention {
            margin-top: 8px;
            margin-bottom: 6px;
        }

        /* Agree + Sign Here: clearer tap targets */
        .agreement-signing-page label[for="agreementCheckbox"],
        .agreement-signing-page #agreementCheckbox + span,
        .agreement-signing-page label:has(#agreementCheckbox) {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            line-height: 1.4;
            padding: 8px 0;
        }

        .agreement-signing-page #agreementCheckbox {
            width: 22px !important;
            height: 22px !important;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .agreement-signing-page #signButton {
            width: 100%;
            min-height: 48px;
            font-size: 16px;
            font-weight: 700;
            margin-top: 8px;
            margin-bottom: 24px;
            border-radius: 0;
        }

        .agreement-signing-page .signature-area {
            padding: 12px;
            margin-top: 12px;
        }

        .agreement-signature-modal-root #sigpad {
            padding: 12px 8px 20px;
            max-width: 100%;
        }

        .agreement-signature-modal-root #sigpad canvas,
        .agreement-signing-page #sigpad canvas {
            min-height: 240px !important;
        }
    }

    /* !important beats per-page body/label { color: black } inline rules. */
    html[data-agreement-theme="dark"] body.agreement-signing-page,
    html[data-agreement-theme="dark"] .agreement-signing-page,
    body.agreement-signing-page[data-theme="dark"],
    body.agreement-signing-page[data-agreement-theme="dark"] {
        color: #f3f4f6 !important;
        background-color: #0f172a !important;
    }

    html[data-agreement-theme="dark"] .agreement-theme-toggle,
    body.agreement-signing-page[data-theme="dark"] .agreement-theme-toggle {
        border-color: #e5e7eb !important;
        background: #111827 !important;
        color: #f9fafb !important;
    }

    html[data-agreement-theme="dark"] .agreement-brand-header,
    body.agreement-signing-page[data-theme="dark"] .agreement-brand-header {
        background-color: #111827 !important;
        border-color: #e5e7eb !important;
        color: #f9fafb !important;
    }

    html[data-agreement-theme="dark"] .agreement-brand-header__address,
    html[data-agreement-theme="dark"] .agreement-brand-header__title,
    body.agreement-signing-page[data-theme="dark"] .agreement-brand-header__address,
    body.agreement-signing-page[data-theme="dark"] .agreement-brand-header__title {
        color: #f9fafb !important;
    }

    html[data-agreement-theme="dark"] .agreement-logo--light,
    body.agreement-signing-page[data-theme="dark"] .agreement-logo--light,
    html[data-agreement-theme="dark"] .agreement-brand-logo-wrap .agreement-logo--light,
    body.agreement-signing-page[data-theme="dark"] .agreement-brand-logo-wrap .agreement-logo--light {
        display: none !important;
    }

    html[data-agreement-theme="dark"] .agreement-logo--dark,
    body.agreement-signing-page[data-theme="dark"] .agreement-logo--dark,
    html[data-agreement-theme="dark"] .agreement-brand-logo-wrap .agreement-logo--dark,
    body.agreement-signing-page[data-theme="dark"] .agreement-brand-logo-wrap .agreement-logo--dark {
        display: block !important;
    }

    html[data-agreement-theme="dark"] .agreement-signing-page .fee-table th,
    body.agreement-signing-page[data-theme="dark"] .fee-table th {
        background-color: #1f2937 !important;
        color: #f9fafb !important;
    }

    html[data-agreement-theme="dark"] .agreement-signing-page .fee-table td,
    body.agreement-signing-page[data-theme="dark"] .fee-table td {
        color: #f3f4f6 !important;
        background-color: #111827 !important;
    }

    html[data-agreement-theme="dark"] .agreement-signing-page .container,
    html[data-agreement-theme="dark"] .agreement-signing-page .header,
    html[data-agreement-theme="dark"] .agreement-signing-page .card,
    html[data-agreement-theme="dark"] .agreement-signing-page .card-header,
    html[data-agreement-theme="dark"] .agreement-signing-page .list-group-item,
    html[data-agreement-theme="dark"] .agreement-signing-page p,
    html[data-agreement-theme="dark"] .agreement-signing-page li,
    html[data-agreement-theme="dark"] .agreement-signing-page td,
    html[data-agreement-theme="dark"] .agreement-signing-page th,
    html[data-agreement-theme="dark"] .agreement-signing-page h1,
    html[data-agreement-theme="dark"] .agreement-signing-page h2,
    html[data-agreement-theme="dark"] .agreement-signing-page h3,
    html[data-agreement-theme="dark"] .agreement-signing-page h4,
    html[data-agreement-theme="dark"] .agreement-signing-page h5,
    html[data-agreement-theme="dark"] .agreement-signing-page label,
    html[data-agreement-theme="dark"] .agreement-signing-page span,
    body.agreement-signing-page[data-theme="dark"] .container,
    body.agreement-signing-page[data-theme="dark"] .header,
    body.agreement-signing-page[data-theme="dark"] .card,
    body.agreement-signing-page[data-theme="dark"] .card-header,
    body.agreement-signing-page[data-theme="dark"] .list-group-item,
    body.agreement-signing-page[data-theme="dark"] p,
    body.agreement-signing-page[data-theme="dark"] li,
    body.agreement-signing-page[data-theme="dark"] td,
    body.agreement-signing-page[data-theme="dark"] th,
    body.agreement-signing-page[data-theme="dark"] label,
    body.agreement-signing-page[data-theme="dark"] span {
        color: #f3f4f6 !important;
        background-color: transparent;
    }

    html[data-agreement-theme="dark"] .agreement-signing-page .card,
    html[data-agreement-theme="dark"] .agreement-signing-page .list-group-item,
    body.agreement-signing-page[data-theme="dark"] .card,
    body.agreement-signing-page[data-theme="dark"] .list-group-item {
        background-color: #1f2937 !important;
        border-color: #4b5563 !important;
    }

    html[data-agreement-theme="dark"] .agreement-signing-page table,
    html[data-agreement-theme="dark"] .agreement-signing-page td,
    html[data-agreement-theme="dark"] .agreement-signing-page th,
    body.agreement-signing-page[data-theme="dark"] table,
    body.agreement-signing-page[data-theme="dark"] td,
    body.agreement-signing-page[data-theme="dark"] th {
        border-color: #4b5563 !important;
    }

    html[data-agreement-theme="dark"] .agreement-expiry-banner,
    body.agreement-signing-page[data-theme="dark"] .agreement-expiry-banner {
        background: #7f1d1d !important;
        color: #fff !important;
    }

    html[data-agreement-theme="dark"] label a,
    body.agreement-signing-page[data-theme="dark"] label a {
        color: #fca5a5 !important;
    }

    html[data-agreement-theme="dark"] .signature-area,
    body.agreement-signing-page[data-theme="dark"] .signature-area {
        background: #1f2937 !important;
    }

    @@media (max-width: 767.98px) {
        html[data-agreement-theme="dark"] .agreement-signing-page .d-md-none .card,
        html[data-agreement-theme="dark"] .agreement-signing-page .d-md-none .card-header,
        html[data-agreement-theme="dark"] .agreement-signing-page .d-md-none .list-group-item,
        body.agreement-signing-page[data-theme="dark"] .d-md-none .card,
        body.agreement-signing-page[data-theme="dark"] .d-md-none .card-header,
        body.agreement-signing-page[data-theme="dark"] .d-md-none .list-group-item {
            background-color: #1f2937 !important;
            color: #f3f4f6 !important;
            border-color: #4b5563 !important;
        }

        html[data-agreement-theme="dark"] .agreement-signing-page .fee-table td:first-child,
        body.agreement-signing-page[data-theme="dark"] .fee-table td:first-child {
            background-color: #111827 !important;
            color: #f3f4f6 !important;
        }

        html[data-agreement-theme="dark"] .agreement-theme-toolbar,
        body.agreement-signing-page[data-theme="dark"] .agreement-theme-toolbar {
            background: #0f172a;
        }
    }

    /* Keep Sign Here usable and obvious once terms are agreed. */
    .agreement-signing-page #signButton:not([disabled]) {
        pointer-events: auto !important;
        opacity: 1 !important;
        cursor: pointer !important;
    }

    .agreement-signing-page #signButton[disabled] {
        opacity: 0.55;
        cursor: not-allowed;
    }
</style>
