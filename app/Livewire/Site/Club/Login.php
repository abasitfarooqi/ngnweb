<?php

namespace App\Livewire\Site\Club;

use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';

    public function render()
    {
        return view('livewire.site.club.login')
            ->layout('components.layouts.public', [
                'title' => 'NGN Club Login | NGN Motors',
            ]);
    }
}
