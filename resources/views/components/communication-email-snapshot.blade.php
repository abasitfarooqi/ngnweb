@props(['html'])

@php
    $html = (string) $html;
    $inject = '<style id="ngn-communication-snapshot">html,body{background:#f3f4f6!important;color:#111827!important;color-scheme:light!important;text-align:center!important;height:auto!important;min-height:0!important;overflow:visible!important}body{margin:0!important}img,.logo{display:block!important;margin-left:auto!important;margin-right:auto!important}.email-outer,.email-wrapper,.email-card,.container{margin-left:auto!important;margin-right:auto!important}.header,.title,.subtitle,h1,h2,h3{text-align:center!important}.email-legacy-html table,table{margin-left:auto!important;margin-right:auto!important}.email-legacy-html td,.email-legacy-html th,td,th{text-align:left!important}</style>';
    if ($html !== '' && ! str_contains($html, 'id="ngn-communication-snapshot"')) {
        if (preg_match('/<head[^>]*>/i', $html)) {
            $html = preg_replace('/(<head[^>]*>)/i', '$1'.$inject, $html, 1) ?? $html;
        } else {
            $html = $inject.$html;
        }
    }
@endphp

<div {{ $attributes->class('communication-email-snapshot mx-auto max-w-[760px] border border-gray-200 bg-[#f3f4f6] dark:border-zinc-700') }}>
    @if($html === '')
        <p class="p-6 text-center text-sm text-gray-500">No email snapshot was stored.</p>
    @else
        <iframe
            title="Email snapshot"
            class="block w-full bg-[#f3f4f6]"
            style="min-height:720px;height:720px;color-scheme:light"
            srcdoc="{!! htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') !!}"
            onload="window.resizeCommunicationEmailFrame && window.resizeCommunicationEmailFrame(this)"
        ></iframe>
    @endif
</div>
