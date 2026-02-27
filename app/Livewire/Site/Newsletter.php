<?php

namespace App\Livewire\Site;

use Livewire\Component;

class Newsletter extends Component
{
    public $email = '';
    public $subscribed = false;

    public function subscribe()
    {
        $this->validate(['email' => 'required|email']);
        $this->subscribed = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.site.newsletter');
    }
}
