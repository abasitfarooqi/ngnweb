<?php

namespace App\Livewire\Agreements;

use Livewire\Component;
use Livewire\Features\SupportPageComponents\PageComponentConfig;
use Livewire\Features\SupportPageComponents\SupportPageComponents;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;

class LegacyMigratedDocument extends Component
{
    public string $legacyView = '';

    /** @var array<string, mixed> */
    public array $documentData = [];

    public function mount(string $legacyView = '', array $documentData = []): void
    {
        $this->legacyView = $legacyView;
        $this->documentData = $documentData;
    }

    public function render()
    {
        return view('livewire.agreements.legacy-document-inner', array_merge(
            $this->documentData,
            ['resolvedView' => $this->resolveLegacyView($this->legacyView)]
        ));
    }

    /**
     * Renders a migrated Blade (full HTML document) inside Livewire’s page pipeline so scripts and snapshots work.
     */
    public static function toResponse(string $legacyView, array $documentData = []): Response
    {
        $layoutConfig = new PageComponentConfig(
            'component',
            'components.layouts.agreement-raw',
            'slot',
            []
        );
        $layoutConfig->normalizeViewNameAndParamsForBladeComponents();

        $html = Livewire::mount(self::class, [
            'legacyView' => $legacyView,
            'documentData' => $documentData,
        ]);

        return response(SupportPageComponents::renderContentsIntoLayout($html, $layoutConfig));
    }

    protected function resolveLegacyView(string $legacyView): string
    {
        if ($legacyView === '') {
            return '';
        }
        if (str_starts_with($legacyView, 'livewire.agreements.migrated.')) {
            return $legacyView;
        }
        if (str_starts_with($legacyView, 'olders.')) {
            $candidate = 'livewire.agreements.migrated.'.substr($legacyView, 7);

            return view()->exists($candidate) ? $candidate : $legacyView;
        }

        return $legacyView;
    }
}
