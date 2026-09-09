<?php

namespace App\Livewire\Site\Contact;

use App\Mail\ContactSubmission;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use App\Livewire\Concerns\HasContactSpamProtection;

class CallBack extends Component
{
    use HasContactSpamProtection;
    public $name = '';

    public $email = '';

    public $phone = '';

    public $preferredTime = '';

    public $message = '';

    public function mount(): void
    {
        $this->startContactSpamProtection();
    }

    public function submitRequest(): void
    {
        $validated = $this->validate([
            'name'          => 'required|string|min:2',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'required|string|min:10',
            'preferredTime' => 'required|string',
            'message'       => 'nullable|string|max:5000',
        ]);
        $this->protectContactSubmission($validated);

        $replyTo = ! empty($validated['email']) ? trim((string) $validated['email']) : config('mail.from.address', 'customerservice@neguinhomotors.co.uk');
        $toEmail = config('mail.from.address', 'customerservice@neguinhomotors.co.uk');

        $body = 'Call back request'."\n\n"
            .'Phone: '.$this->phone."\n"
            .($this->email !== '' ? 'Email: '.$this->email."\n" : '')
            .'Preferred time: '.$this->preferredTime."\n\n"
            .'Message:'."\n".($this->message !== '' ? $this->message : '—');

        try {
            Mail::to($toEmail)->send(new ContactSubmission(
                senderName: $this->name,
                senderEmail: $replyTo,
                phone: $this->phone,
                topic: 'Call back request',
                messageBody: $body,
                branchName: '',
            ));
        } catch (\Exception $e) {
            \Log::error('Call back form mail failed: '.$e->getMessage());
        }

        session()->flash('success', 'Callback request received. We\'ll call you shortly.');
        $this->reset(['name', 'email', 'phone', 'preferredTime', 'message']);
        $this->resetContactSpamProtection();
    }

    public function render()
    {
        return view('livewire.site.contact.call-back')
            ->layout('components.layouts.public', [
                'title' => 'Request a Call Back | NGN Motors',
            ]);
    }
}
