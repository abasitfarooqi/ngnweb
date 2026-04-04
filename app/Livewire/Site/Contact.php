<?php

namespace App\Livewire\Site;

use App\Mail\ContactSubmission;
use App\Models\Branch;
use Illuminate\Support\Facades\Mail;
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

    /**
     * Alias for older Blade / Flux forms that still call submitRequest.
     */
    public function submitRequest(): void
    {
        $this->submit();
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'name' => 'required|min:2',
            'email' => 'required|email',
            'phone' => 'required',
            'branch_id' => 'nullable|exists:branches,id',
            'topic' => 'required',
            'message' => 'required|min:10',
        ]);

        $branchName = '';
        if ($this->branch_id) {
            $branch = Branch::find($this->branch_id);
            $branchName = $branch?->name ?? '';
        }

        $toEmail = config('mail.from.address', 'customerservice@neguinhomotors.co.uk');

        try {
            Mail::to($toEmail)->send(new ContactSubmission(
                senderName: $this->name,
                senderEmail: $this->email,
                phone: $this->phone,
                topic: $this->topic,
                messageBody: $this->message,
                branchName: $branchName,
            ));
        } catch (\Exception $e) {
            \Log::error('Contact form mail failed: '.$e->getMessage());
        }

        session()->flash('success', 'Thank you for your message. We will get back to you soon.');
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
