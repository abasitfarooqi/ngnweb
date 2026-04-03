<?php

namespace App\Livewire\Agreements;

use Livewire\Component;

class LegacyRenderer extends Component
{
    public string $legacyView = '';

    public array $payload = [];

    public function mount(string $legacyView, array $payload = []): void
    {
        $this->legacyView = $legacyView;
        $this->payload = $payload;
    }

    public function render()
    {
        return view('livewire.agreements.components.legacy-renderer', array_merge(
            ['legacyView' => $this->legacyView],
            $this->payload
        ));
    }
}
