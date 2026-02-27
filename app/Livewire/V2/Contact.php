<?php

namespace App\Livewire\V2;

use App\Http\Controllers\Welcome\ContactController;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Contact extends Component
{
    #[Rule('required|string|max:100')]
    public string $name = '';

    #[Rule('required|email|max:150')]
    public string $email = '';

    #[Rule('required|string|max:20')]
    public string $phone = '';

    #[Rule('required|string|min:10|max:1000')]
    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        // TODO: store message and send notification
        $this->submitted = true;

        $this->reset(['name', 'email', 'phone', 'message']);
    }

    public function render()
    {
        return view('livewire.v2.contact')
            ->layout('v2.layouts.app');
    }
}
