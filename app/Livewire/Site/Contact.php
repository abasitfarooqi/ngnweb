<?php

namespace App\Livewire\Site;

use App\Models\Branch;
use Livewire\Component;

class Contact extends Component
{
    public $branches;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $branch_id = '';
    public $topic = '';
    public $message = '';

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function submit()
    {
        $this->validate([
            'name'      => 'required|min:2',
            'email'     => 'required|email',
            'phone'     => 'required',
            'branch_id' => 'nullable|exists:branches,id',
            'topic'     => 'required',
            'message'   => 'required|min:10',
        ]);

        session()->flash('success', 'Thank you for your message. We\'ll get back to you soon.');
        $this->reset(['name', 'email', 'phone', 'branch_id', 'topic', 'message']);
    }

    public function render()
    {
        return view('livewire.site.contact')
            ->layout('components.layouts.public', [
                'title' => 'Contact Us | NGN Motors London',
                'description' => 'Get in touch with NGN Motors. Visit us at Catford, Tooting, or Sutton.',
            ]);
    }
}
