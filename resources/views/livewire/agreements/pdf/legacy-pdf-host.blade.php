@php
    $legacyPdfView = (string) ($legacyPdfView ?? '');
    $candidateMigratedPdfView = '';
    if (str_starts_with($legacyPdfView, 'livewire.agreements.pdf.templates.')) {
        $candidateMigratedPdfView = $legacyPdfView;
    } elseif (str_starts_with($legacyPdfView, 'olders.pdf.')) {
        $candidateMigratedPdfView = 'livewire.agreements.pdf.templates.'.substr($legacyPdfView, 11);
    }
@endphp

@if($legacyPdfView !== '')
    @if($candidateMigratedPdfView !== '' && view()->exists($candidateMigratedPdfView))
        @include($candidateMigratedPdfView)
    @elseif(view()->exists($legacyPdfView))
        @include($legacyPdfView)
    @endif
@endif
