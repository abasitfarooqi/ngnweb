<?php

namespace App\Livewire\FluxAdmin\Components;

use Livewire\Component;

/**
 * Thin wrapper around Chart.js rendered via Alpine. Parent pages supply
 * labels + datasets; the browser draws the canvas. Keeping this as a
 * proper Livewire component (not anonymous Blade) so parent pages can
 * refresh data with $refresh.
 */
class FluxChart extends Component
{
    public string $canvasId;

    public string $type = 'line';

    /** @var array<int, string> */
    public array $labels = [];

    /** @var array<int, array<string, mixed>> */
    public array $datasets = [];

    /** @var array<string, mixed> */
    public array $options = [];

    public string $height = '320px';

    /**
     * @param  array<int, string>  $labels
     * @param  array<int, array<string, mixed>>  $datasets
     * @param  array<string, mixed>  $options
     */
    public function mount(
        string $canvasId,
        string $type = 'line',
        array $labels = [],
        array $datasets = [],
        array $options = [],
        string $height = '320px',
    ): void {
        $this->canvasId = $canvasId;
        $this->type = $type;
        $this->labels = $labels;
        $this->datasets = $datasets;
        $this->options = $options;
        $this->height = $height;
    }

    public function render()
    {
        return view('flux-admin.components.flux-chart-component');
    }
}
