<?php

namespace App\Livewire\Site;

use App\Models\Branch;
use Livewire\Component;

class Footer extends Component
{
    public $branches;
    public $email = '';
    public $consent = false;

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function subscribe()
    {
        $this->validate([
            'email'   => 'required|email',
            'consent' => 'accepted',
        ]);

        session()->flash('newsletter_success', 'Thank you for subscribing!');
        $this->email   = '';
        $this->consent = false;
    }

    public function render()
    {
        return view('livewire.site.footer');
    }
}
