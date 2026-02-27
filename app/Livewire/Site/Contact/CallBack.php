<?php

namespace App\Livewire\Site\Contact;

use App\Models\Branch;
use Livewire\Component;

class CallBack extends Component
{
    public $branches;
    public $name = '';
    public $phone = '';
    public $branch_id = '';
    public $preferred_time = '';
    public $topic = '';

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function submit()
    {
        $this->validate([
            'name'  => 'required|string|min:2',
            'phone' => 'required|string|min:10',
            'topic' => 'required|string',
        ]);
        session()->flash('success', 'Callback request received. We\'ll call you shortly.');
        $this->reset(['name', 'phone', 'branch_id', 'preferred_time', 'topic']);
    }

    public function render()
    {
        return view('livewire.site.contact.call-back')
            ->layout('components.layouts.public', [
                'title' => 'Request a Call Back | NGN Motors',
            ]);
    }
}
