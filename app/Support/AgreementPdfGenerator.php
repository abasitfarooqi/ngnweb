<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;

final class AgreementPdfGenerator
{
    public static function loadView(string $view, array $data = []): mixed
    {
        $templatesPrefix = 'livewire.agreements.pdf.templates.';
        $resolvedView = str_starts_with($view, 'pdf.')
            ? $templatesPrefix.substr($view, 4)
            : $view;
        $resolvedData = array_merge(AgreementPdfViewAssets::composerVariables(), AgreementDateTime::preparePdfData($data, true));

        if (! view()->exists($resolvedView)) {
            $resolvedView = 'livewire.agreements.pdf.legacy-pdf-host';
            $resolvedData = array_merge($resolvedData, [
                'legacyPdfView' => str_starts_with($view, 'pdf.')
                    ? $templatesPrefix.substr($view, 4)
                    : $view,
            ]);
        }

        if (config('agreement.pdf_engine', 'dompdf') === 'browsershot') {
            return new RemembersPdfSavePath(new BrowsershotPdfAdapter($resolvedView, $resolvedData));
        }

        return new RemembersPdfSavePath(
            Pdf::loadView($resolvedView, $resolvedData)
                ->setOption('isRemoteEnabled', false)
                ->setOption('isPhpEnabled', true)
                ->setOption('chroot', base_path())
        );
    }
}
