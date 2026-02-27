<?php

namespace App\Livewire\V2;

use Livewire\Attributes\Rule;
use Livewire\Component;

class MotChecker extends Component
{
    #[Rule('required|string|min:2|max:10')]
    public string $reg = '';

    public bool $loading = false;
    public ?array $result = null;
    public ?string $error = null;

    public function check(): void
    {
        $this->validate();
        $this->loading = true;
        $this->error = null;
        $this->result = null;

        $this->dispatch('mot-check-submitted', reg: $this->reg);
    }

    public function render()
    {
        return view('livewire.v2.mot-checker')
            ->layout('v2.layouts.app');
    }
}
